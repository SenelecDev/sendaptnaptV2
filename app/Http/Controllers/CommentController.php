<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Demande;
use App\Models\Note;
use App\Notifications\WorkflowNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class CommentController extends Controller
{
    /**
     * Ajouter un commentaire
     */
    public function store(Request $request)
    {
        $request->validate([
            'commentable_type' => 'required|in:demande,note',
            'commentable_id' => 'required|integer',
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
            'is_internal' => 'boolean',
        ]);

        // Résoudre le modèle
        $model = $request->commentable_type === 'demande' 
            ? Demande::findOrFail($request->commentable_id)
            : Note::findOrFail($request->commentable_id);

        $comment = Comment::create([
            'commentable_type' => get_class($model),
            'commentable_id' => $model->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'is_internal' => $request->is_internal ?? false,
        ]);

        // Notifier les utilisateurs concernés
        $this->notifyParticipants($comment, $model);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment->load('user'),
            ]);
        }

        return back()->with('success', 'Commentaire ajouté');
    }

    /**
     * Supprimer un commentaire (auteur ou admin seulement)
     */
    public function destroy(Comment $comment)
    {
        $user = auth()->user();
        
        if ($comment->user_id !== $user->id && !$user->hasRole('admin')) {
            abort(403);
        }

        $comment->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Commentaire supprimé');
    }

    /**
     * Récupérer les commentaires d'un élément
     */
    public function index(Request $request)
    {
        $request->validate([
            'commentable_type' => 'required|in:demande,note',
            'commentable_id' => 'required|integer',
        ]);

        $modelClass = $request->commentable_type === 'demande' 
            ? Demande::class 
            : Note::class;

        $query = Comment::where('commentable_type', $modelClass)
            ->where('commentable_id', $request->commentable_id)
            ->root()
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc');

        // Filtrer les commentaires internes pour les demandeurs
        if (auth()->user()->hasRole('demandeur') && !auth()->user()->hasRole(['admin', 'desa'])) {
            $query->public();
        }

        return response()->json($query->get());
    }

    /**
     * Notifier les participants de la discussion
     */
    protected function notifyParticipants(Comment $comment, $model)
    {
        $recipients = collect();

        // Ajouter le demandeur (si pas commentaire interne)
        if (!$comment->is_internal && $model instanceof Demande && $model->demandeur) {
            if ($model->demandeur_id !== auth()->id()) {
                $recipients->push($model->demandeur);
            }
        }

        // Si c'est une note, notifier l'éditeur
        if ($model instanceof Note && $model->etabliPar && $model->etabli_id !== auth()->id()) {
            $recipients->push($model->etabliPar);
        }

        // Si c'est une réponse, notifier l'auteur du commentaire parent
        if ($comment->parent_id) {
            $parent = Comment::find($comment->parent_id);
            if ($parent && $parent->user_id !== auth()->id()) {
                $recipients->push($parent->user);
            }
        }

        $recipients = $recipients->unique('id');

        if ($recipients->isNotEmpty()) {
            $type = $model instanceof Demande ? 'DAPT' : 'NAPT';
            $numero = $model instanceof Demande ? $model->numero_demande : $model->numero_note;

            Notification::send($recipients, new WorkflowNotification(
                type: 'comment',
                title: 'Nouveau commentaire',
                message: auth()->user()->name . " a commenté sur {$type} {$numero}",
                actionUrl: $model instanceof Demande 
                    ? route('demandeur.demandes.show', $model->id)
                    : route('desa.notes.show', $model->id),
                actionText: 'Voir le commentaire',
                data: ['comment_id' => $comment->id]
            ));
        }
    }
}
