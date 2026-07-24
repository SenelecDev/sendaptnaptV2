<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Plaquette documentation SENDAPTNAPT</title>
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; box-sizing: border-box; }
        body { font-size: 10px; color: #1f2937; line-height: 1.45; margin: 0; padding: 0; }
        .header {
            background: #2B1444;
            color: #fff;
            padding: 18px 22px;
            margin-bottom: 16px;
        }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 4px 0 0; font-size: 10px; color: #e5e7eb; }
        .brand { font-size: 9px; color: #F06A00; margin-bottom: 4px; letter-spacing: 0.5px; }
        h2 {
            font-size: 12px;
            color: #2B1444;
            border-bottom: 2px solid #B3006C;
            padding-bottom: 4px;
            margin: 16px 0 8px;
        }
        h3 { font-size: 11px; color: #B3006C; margin: 10px 0 4px; }
        p, li { margin: 0 0 4px; }
        ul, ol { margin: 0 0 8px; padding-left: 16px; }
        .box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-left: 3px solid #B3006C;
            padding: 8px 10px;
            margin: 8px 0;
        }
        .box-teal { border-left-color: #0A91A3; }
        .box-orange { border-left-color: #E87400; }
        table { width: 100%; border-collapse: collapse; margin: 6px 0 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #2B1444; color: #fff; font-size: 9px; }
        td { font-size: 9px; }
        .cols { width: 100%; }
        .cols td { width: 50%; border: none; padding: 0 6px 0 0; vertical-align: top; }
        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #6b7280;
            text-align: center;
        }
        .muted { color: #6b7280; }
        strong { color: #111827; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">SENELEC — DESA / DESE</div>
        <h1>Plaquette documentation SENDAPTNAPT</h1>
        <p>Guide utilisateur — Demandes d'Arrêt Pour Travaux (DAPT) &amp; Notes d'Arrêt Pour Travaux (NAPT)</p>
        <p style="margin-top:6px;font-size:8px;opacity:.85;">Générée le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <h2>1. Introduction</h2>
    <p><strong>SENDAPTNAPT</strong> gère le cycle électronique des DAPT et NAPT. Connexion locale puis LDAP si activé. Multi-rôles : toutes les sections apparaissent dans la barre latérale.</p>
    <div class="box">
        <strong>DAPT</strong> — demande formelle d'arrêt programmé d'équipements.<br>
        <strong>NAPT</strong> — note officielle établie par le DESA jusqu'à l'exécution.
    </div>

    <h2>2. Workflow général</h2>
    <ol>
        <li><strong>Demandeur</strong> crée / soumet une DAPT (schéma obligatoire).</li>
        <li><strong>DESA</strong> traite : prise en charge, Faire NAPT, ou retour.</li>
        <li><strong>Vérificateur</strong> vérifie (statut vérifiée) ou retourne.</li>
        <li><strong>Valideur</strong> valide (NAPT validée + DAPT acceptée) ou retourne.</li>
        <li><strong>Opérateur Chef</strong> joint la fiche manœuvre (PDF/JPG/PNG).</li>
        <li><strong>Opérateur</strong> démarre puis termine l'exécution.</li>
    </ol>
    <div class="box box-orange">
        Règles : une DAPT = une NAPT · document d'étude obligatoire si étude = oui · exécution bloquée sans fiche manœuvre.
    </div>

    <h2>3. Statuts</h2>
    <table class="cols">
        <tr>
            <td>
                <h3>DAPT</h3>
                <ul>
                    <li>brouillon, créée</li>
                    <li>en cours de traitement</li>
                    <li>acceptée, retournée</li>
                </ul>
            </td>
            <td>
                <h3>NAPT</h3>
                <ul>
                    <li>brouillon, en étude, en attente de vérification</li>
                    <li>vérifiée, validée</li>
                    <li>en cours d'exécution, exécutée</li>
                    <li>retournée, annulée</li>
                </ul>
            </td>
        </tr>
    </table>

    <h2>4. Rôles &amp; menus</h2>
    <table>
        <thead>
            <tr><th>Rôle</th><th>Menus / responsabilités</th></tr>
        </thead>
        <tbody>
            <tr><td>Demandeur</td><td>Demandes, Absences, Observations — créer / suivre DAPT (soi + groupe)</td></tr>
            <tr><td>DESA (Éditeur)</td><td>Diffusions, Demandes, Notes — traiter, créer NAPT, annuler, exporter</td></tr>
            <tr><td>Vérificateur</td><td>Notes — vérifier ou retourner</td></tr>
            <tr><td>Valideur</td><td>Notes — valider ou retourner ; signature profil sur PDF</td></tr>
            <tr><td>Opérateur Chef</td><td>Fiche manœuvre, annulation si validée</td></tr>
            <tr><td>Opérateur</td><td>Démarrer / terminer exécution</td></tr>
            <tr><td>Directeur</td><td>Consultation DAPT/NAPT, statistiques, Feedback</td></tr>
            <tr><td>Admin</td><td>Utilisateurs, groupes, référentiels, intérims, journal ; super admin : sync + impersonation</td></tr>
        </tbody>
    </table>

    <h2>5. Créer une DAPT</h2>
    <p>Menu <strong>Demandeur → Demandes → Nouvelle demande</strong>.</p>
    <ol>
        <li>Période, destinataire, désignation, schéma (obligatoire).</li>
        <li>Ouvrages : mode <strong>GMAO</strong> (lieu + équipements) ou <strong>manuel</strong>.</li>
        <li>Chargé de travaux interne/externe, téléphones, MTE/MCCE, UE/DE, restitution le soir si besoin.</li>
        <li><strong>Enregistrer brouillon</strong> ou <strong>Valider et soumettre</strong> (statut créée).</li>
    </ol>

    <h2>6. Traitement DESA / NAPT</h2>
    <ul>
        <li>Filtres Demandes : Reçues, En cours, Retournées, Acceptées.</li>
        <li>Actions : prendre en charge, Faire NAPT, retourner au demandeur.</li>
        <li>NAPT : n°, semaine, dates, étude + document, consignations / correspondants / services.</li>
        <li>Envoi en vérification → dates acceptées + PDF DAPT régénéré.</li>
    </ul>

    <h2>7. Diffusion hebdomadaire</h2>
    <div class="box box-teal">
        Menu <strong>Éditeur → Diffusions</strong> : choisir semaine / année / statut, sélectionner les groupes,
        prévisualiser, puis envoyer (email + PDF combiné des NAPT).
    </div>

    <h2>8. Retours, annulations, exécution</h2>
    <ul>
        <li><strong>Retours DAPT</strong> : DESA → demandeur. <strong>Retours NAPT</strong> : vérificateur ou valideur → DESA.</li>
        <li><strong>Annulation</strong> : DESA (motif ≥ 10 car.), Opérateur Chef (si validée), Opérateur (validée / en cours).</li>
        <li><strong>Fiche manœuvre</strong> : PDF/JPG/PNG max 10 Mo, obligatoire avant démarrage.</li>
        <li><strong>Exécution</strong> : dates réelles, ou créneaux si restitution le soir.</li>
    </ul>

    <h2>9. Outils communs</h2>
    <ul>
        <li>Recherche header (n° DAPT/NAPT, désignation, demandeur…)</li>
        <li>Calendrier NAPT, Export Excel/PDF (/exports)</li>
        <li>Profil &amp; signature (PNG/JPG max 2 Mo)</li>
        <li>Absences / intérims, Mes observations, Notifications</li>
        <li>Assistant intelligent (chat bas-droite) + page Documentation en ligne</li>
    </ul>

    <h2>10. Administration &amp; Directeur</h2>
    <p><strong>Directeur</strong> : dashboards et stats (semaine/mois/année), Feedback — consultation.</p>
    <p><strong>Admin</strong> : utilisateurs, groupes, chargés consignation, correspondants, services, observations,
        intérims, gestion DAPT/NAPT, journal. Super admin : Sync Oracle/LDAP/photos et impersonation.</p>

    <h2>11. Bonnes pratiques</h2>
    <ol>
        <li>Schéma clair ; préférer GMAO si l'équipement existe.</li>
        <li>Brouillon si incomplet ; soumettre seulement quand prêt.</li>
        <li>Document d'étude si étude = oui avant vérification.</li>
        <li>Fiche manœuvre avant démarrage terrain.</li>
        <li>Signature profil pour les PDF NAPT.</li>
        <li>Déclarer absences avec intérimaire à l'avance.</li>
        <li>Filtrer avant export ou diffusion.</li>
    </ol>

    <div class="footer">
        SENDAPTNAPT — Plaquette documentation utilisateur — SENELEC<br>
        Document généré automatiquement depuis l'application. Pour le détail interactif : menu Outils → Documentation.
    </div>
</body>
</html>
