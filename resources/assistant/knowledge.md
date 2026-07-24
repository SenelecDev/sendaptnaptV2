# Guide SENDAPTNAPT (assistant)

## Qu'est-ce que SENDAPTNAPT ?
Application SENELEC de gestion électronique des **DAPT** (Demandes d'Arrêt Pour Travaux) et des **NAPT** (Notes d'Arrêt Pour Travaux). Cycle : Demandeur → DESA → Vérificateur → Valideur → Opérateur Chef → Opérateur.

Connexion : login local puis LDAP si activé. Multi-rôles : toutes les sections apparaissent dans la barre latérale. Rôle demandeur par défaut si aucun rôle.

## Menus réels (sidebar)
- Demandeur : Dashboard, **Demandes**, Absences, Observations
- Éditeur (DESA) : Dashboard, **Diffusions**, Demandes (filtres statut), Notes (filtres statut), Absences, Observations
- Vérificateur / Valideur / Opérateur Chef / Opérateur : Dashboard, Notes, Absences, Observations
- Directeur : Dashboard, DAPT, NAPT, Feedback
- Admin : Dashboard, Utilisateurs, Groupes, Chargés consignation, Correspondants, Services, Observations, Intérims, Gestion DAPT, Gestion NAPT, Journal
- Outils (tous) : Calendrier NAPT, Export Excel, Documentation

## Workflow global
1. Demandeur crée une DAPT (brouillon ou soumise → créée). Schéma image obligatoire.
2. DESA traite : mettre en cours, accepter les dates, **Faire NAPT**, ou retourner au demandeur.
3. DESA rédige la NAPT (brouillon → en étude → en attente de vérification). À l'envoi en vérification, dates acceptées sur DAPT + PDF DAPT régénéré.
4. Vérificateur : **vérifie** (statut vérifiée) ou **retourne** au DESA avec motif.
5. Valideur : **valide** (NAPT validée, DAPT acceptée) ou **retourne** au DESA.
6. Opérateur Chef joint la **fiche manœuvre** (PDF/JPG/PNG, max 10 Mo).
7. Opérateur démarre puis termine l'exécution (dates réelles). Sans fiche manœuvre = démarrage bloqué.
8. Annulation possible selon rôle/statut (motif ≥ 10 caractères pour DESA).

## Règles importantes
- Une DAPT = **une seule NAPT** (réédition si déjà liée).
- Si **étude = oui**, document d'étude obligatoire avant envoi vérification / avant vérifier / valider.
- Demandeur ne modifie une DAPT que si **retournée** ou **brouillon**.
- Visibilité demandeur : ses DAPT + celles de son **groupe**.
- Exécution bloquée sans fiche manœuvre.

## Statuts DAPT
créée | en cours de traitement | acceptée | retournée | brouillon

## Statuts NAPT
brouillon | en étude | en attente de vérification | vérifiée | en attente de validation | validée | en cours d'exécution | exécutée | retournée | annulée

## Glossaire
- **DAPT** : Demande d'Arrêt Pour Travaux
- **NAPT** : Note d'Arrêt Pour Travaux
- **GMAO** : mode de saisie des ouvrages depuis le référentiel équipements (SQL Server)
- **MTE** : Mesures Techniques d'Exploitation (oui/non sur la DAPT)
- **MCCE** : Mesures de Consignation / Contrôle Électrique (oui/non)
- **UE / DE** : étape / unité d'exécution (champ étape de la DAPT)
- **Restitution le soir** (`dmrp_restitution`) : travaux avec restitution quotidienne → exécution par créneaux (slots)
- **Chargé de travaux** : responsable terrain (interne annuaire ou externe)
- **Chargé de consignation / correspondant / service destinataire** : destinataires de la NAPT (référentiels admin)
- **Fiche manœuvre** : document joint par l'opérateur chef avant exécution
- **Intérim** : délégation temporaire d'un rôle pendant une absence

## Rôles
- **Demandeur** : créer/suivre DAPT (soi + groupe), corriger si retournée, absences, observations.
- **DESA (Éditeur)** : traiter DAPT, créer/éditer NAPT, retours, annulation, **Diffusions** hebdo, exports PDF listes/dashboard.
- **Vérificateur** : vérifier ou retourner les NAPT en attente de vérification.
- **Valideur** : valider ou retourner ; signature profil utilisée sur PDF.
- **Opérateur Chef** : fiche manœuvre, annuler si validée.
- **Opérateur** : démarrer/terminer exécution, annuler si validée ou en cours.
- **Directeur** : consultation DAPT/NAPT, statistiques (semaine/mois/année), Feedback.
- **Admin** : utilisateurs, groupes, référentiels, intérims, observations, journal, gestion DAPT/NAPT, exports. Super admin : sync Oracle/LDAP/photos + impersonation.

## Créer une DAPT (pas à pas)
1. Menu Demandeur → **Demandes** → Nouvelle demande.
2. Période prévue (dates/heures), destinataire (DESA/DD), désignation.
3. **Schéma** (image) obligatoire.
4. Mode ouvrages : **GMAO** ou **manuel**.
5. Chargé de travaux interne ou externe + téléphones.
6. Options : MTE, MCCE, étape UE/DE, restitution le soir si applicable.
7. **Enregistrer brouillon** (statut brouillon, modifiable) OU **Valider et soumettre** (statut créée → DESA).

## Mode GMAO
Sur le formulaire DAPT, choisir mode GMAO :
1. Sélectionner le lieu d'exécution (recherche référentiel).
2. Choisir ouvrages à consigner / installer (équipements enfants, lignes, postes selon le type).
3. Les données viennent de la GMAO (API interne lieux / équipements / lignes).
Mode manuel : saisie libre des ouvrages à consigner et à installer.

## Traiter une DAPT (DESA)
Menu Éditeur → Demandes (filtres : Reçues, En cours, Retournées, Acceptées).
Actions typiques :
- Prendre en charge (en cours de traitement)
- Accepter les dates / **Faire NAPT** (crée ou ouvre la NAPT ; DAPT souvent acceptée)
- Retourner au demandeur avec motif
Sélection alternative : Notes → sélectionner une DAPT sans NAPT.

## Créer / traiter une NAPT (DESA)
1. Faire NAPT depuis la DAPT ou Notes → créer.
2. Renseigner : numéro, semaine, dates travaux/retrait, étude oui/non (+ document si oui).
3. Destinataires : chargés de consignation, correspondants, services.
4. Sauvegarder brouillon / mettre en étude / envoyer en vérification.
5. Si retournée : corriger puis renvoyer.

## Vérification
Menu Vérificateur → Notes. Ouvrir NAPT en attente de vérification.
- Vérifier → statut **vérifiée**
- Retourner → motif obligatoire → DESA (statut retournée)

## Validation
Menu Valideur → Notes. Ouvrir NAPT vérifiée.
- Valider → NAPT **validée**, DAPT **acceptée**
- Retourner → motif (2ᵉ niveau) → DESA
Signature : Profil → Ma signature (PNG/JPG, ~300×200 px, max 2 Mo) pour les PDF.

## Fiche manœuvre & exécution
Opérateur Chef : NAPT validée → joindre fiche (PDF/JPG/PNG, max 10 Mo) ; modification/suppression possibles avant exécution.
Opérateur :
1. Démarrer : date/heure réelle de début (`dre_reel`)
2. Terminer : dates réelles de fin, ou **créneaux** si restitution le soir
Statut final : exécutée.

## Retours & annulations
Retours DAPT : DESA → demandeur (motif).
Retours NAPT : vérificateur ou valideur → DESA.
Annulations NAPT :
- DESA : presque tous statuts sauf déjà exécutée/annulée (motif ≥ 10 car.)
- Opérateur Chef : si validée
- Opérateur : si validée ou en cours d'exécution

## Diffusion hebdomadaire (DESA)
Menu Éditeur → **Diffusions**.
1. Choisir semaine, année, filtre statut (optionnel)
2. Sélectionner les groupes destinataires
3. **Prévisualiser** la liste des NAPT
4. **Envoyer** : email aux groupes + PDF combiné des NAPT de la semaine
Les groupes sans email correctement configurés ne reçoivent pas le mail.

## Directeur
Menu Directeur :
- Dashboard : indicateurs filtrables (semaine / mois / année)
- DAPT / NAPT : listes + détail (lecture)
- Statistiques DAPT et NAPT dédiées
- Feedback : envoyer un retour / observation de supervision
Accès en consultation / supervision, pas d'édition workflow.

## Administration
Menu Admin :
- **Utilisateurs** : CRUD, rôles, groupes ; bouton Sync (super admin) ; Simuler (impersonation super admin)
- **Groupes** : organisation + membres (filtres exports / diffusion)
- **Chargés de consignation / Correspondants / Services** : référentiels NAPT
- **Observations** : traiter bugs/suggestions (ouvrir → en cours → résolu)
- **Intérims** : toutes les absences
- **Gestion DAPT / NAPT** : supervision, exports, timelines/historique
- **Journal d'activités** : audit
Sync Oracle/LDAP/photos : page Utilisateurs → Sync (super admin uniquement).
Impersonation : Simuler un user → bannière → Arrêter l'impersonation.

## Exports
Outils → **Export Excel** (/exports) :
- DAPT Excel : dates, statut, groupe
- NAPT Excel + PDF : recherche n°, demandeur, ouvrage, type, dates, semaine, année, statut, groupe
Autres : DESA export PDF listes filtrées + dashboard ; Admin exports Excel + dashboard ; PDF unitaire depuis détail DAPT/NAPT.

## Calendrier NAPT
Outils → Calendrier NAPT : vue planifiée des notes (événements AJAX). Utile pour visualiser la charge hebdomadaire.

## Recherche
Barre du header (min. 2 caractères) : n° DAPT, désignation, lieu, destinataire, demandeur (nom/matricule) ; n° NAPT, semaine ; utilisateurs si admin.

## Observations & Feedback
- **Mes observations** (tous) : bug / suggestion / question → suivi personnel
- **Feedback Directeur** : retours de supervision
- **Observations admin** : traitement et clôture
Utiliser aussi le bouton « Envoyer une observation » en bas de la Documentation.

## Intérims & absences
Menu **Absences** → Nouvelle absence : dates, rôle(s) à déléguer (ou tous), intérimaire, motif.
Badge **INTÉRIM** dans la sidebar. L'intérimaire a les droits du rôle pendant la période. Notifications à l'attribution et à la fin. Admin voit toutes les absences.

## Notifications
Header (compteur) + page Notifications. In-app + email si activé.
Types : DAPT créée/acceptée/retournée ; NAPT soumise/vérifiée/validée/retournée/exécutée/annulée ; intérim ; réponse feedback/observation.
Actions : marquer lu, tout lire, supprimer. Rappels cron possibles pour dossiers en attente/retard.

## Profil & signature
Profil : infos personnelles.
Ma signature : PNG/JPG recommandé fond transparent 300×200, max 2 Mo. Utilisée sur les PDF NAPT (valideur / acteurs).

## Assistant intelligent
Bouton violet/magenta bas-droite + icône chat header.
Mode local (documentation + vos DAPT/NAPT/file d'attente) même sans Internet.
Gemini optionnel si GEMINI_API_KEY + réseau Google AI.

## Tutoriel d'accueil (onboarding)
Au premier login, un tutoriel guidé selon le rôle (demandeur, DESA, autres). Peut être complété puis ne plus s'afficher.

## Historique / timeline
Sur certaines fiches DAPT/NAPT (admin, directeur, détail) : historique des changements de statut et acteurs. Utile pour audit et suivi des retours.

## Tutos rapides
1. Créer DAPT : Demandeur → Demandes → Nouvelle demande → Valider et soumettre.
2. Brouillon : Enregistrer brouillon puis soumettre plus tard.
3. Faire NAPT : DESA → Demandes → Faire NAPT → envoyer vérification.
4. Vérifier / Valider : menus Vérificateur / Valideur.
5. Fiche manœuvre : Opérateur Chef → joindre fichier.
6. Exécuter : Opérateur → Démarrer puis Terminer.
7. Diffusion : DESA → Diffusions → prévisualiser → envoyer.
8. Export : Outils → Export Excel.
9. Signature : Profil → Ma signature.
10. Intérim : Absences → Nouvelle absence.
11. Aide : Documentation (/documentation) ou ce chat.
