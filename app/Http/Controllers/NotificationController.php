<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\WorkflowNotification;

class NotificationController extends Controller
{
    /**
     * Afficher toutes les notifications de l'utilisateur
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->notifications();
        
        // Filtre par statut
        $statut = $request->get('statut', 'tous');
        if ($statut === 'non_lues') {
            $query = $user->unreadNotifications();
        } elseif ($statut === 'lues') {
            $query = $user->readNotifications();
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $stats = [
            'total' => $user->notifications()->count(),
            'non_lues' => $user->unreadNotifications()->count(),
            'lues' => $user->readNotifications()->count(),
        ];
        
        return view('notifications.index', compact('notifications', 'stats', 'statut'));
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
        }
        
        // Rediriger vers l'action_url si présent
        if ($notification && isset($notification->data['action_url'])) {
            return redirect($notification->data['action_url']);
        }
        
        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Supprimer une notification
     */
    public function destroy(string $id)
    {
        Auth::user()->notifications()->where('id', $id)->delete();
        
        return redirect()->back()->with('success', 'Notification supprimée.');
    }

    /**
     * Supprimer toutes les notifications lues
     */
    public function destroyRead()
    {
        Auth::user()->readNotifications()->delete();
        
        return redirect()->back()->with('success', 'Notifications lues supprimées.');
    }

    /**
     * API: Obtenir les notifications non lues (pour AJAX)
     */
    public function getUnreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * API: Obtenir les dernières notifications (pour dropdown)
     */
    public function getLatest()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'info',
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'action_url' => $notification->data['action_url'] ?? null,
                    'read' => $notification->read_at !== null,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'icon' => WorkflowNotification::getIcon($notification->data['type'] ?? ''),
                    'color' => WorkflowNotification::getColor($notification->data['type'] ?? ''),
                ];
            });
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
}
