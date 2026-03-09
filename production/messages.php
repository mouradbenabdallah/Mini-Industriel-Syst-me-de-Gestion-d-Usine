<?php
/**
 * Messages du module Production
 * Système de messagerie interne pour les utilisateurs du module production
 * 
 * Fonctionnalités:
 * - Réception et envoi de messages
 * - Boîte de réception et boîte d'envoi
 * 
 * Projet: Usine Industriel - Système de gestion d'usine
 * Mode: Procédural (sans POO)
 */

// Démarrage de la session
session_start();

// Rôles autorisés: admin, manager et employé
$allowed_roles = ['admin','manager','employe'];

// Inclusion de la configuration de la base de données
require_once '../config/database.php';

// Inclusion du fichier de vérification d'authentification
require_once '../includes/auth_check.php';

// Récupération de l'ID de l'utilisateur connecté
$user_id = $_SESSION['user']['id'];

// Marquer tous les messages non lus comme lus
$pdo->prepare('UPDATE messages SET lu = 1 WHERE destinataire_id = ?')->execute([$user_id]);

// Récupération des messages reçus (boîte de réception) - sans JOIN
$stmt = $pdo->prepare('SELECT * FROM messages WHERE destinataire_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$inbox = $stmt->fetchAll();

// Pour chaque message reçu, récupérer le nom de l'expéditeur séparément
foreach ($inbox as &$msg) {
    $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
    $stmt->execute([$msg['expediteur_id']]);
    $user = $stmt->fetch();
    $msg['expediteur_nom'] = $user['nom'] ?? 'Inconnu';
}

// Récupération des messages envoyés (boîte d'envoi) - sans JOIN
$stmt = $pdo->prepare('SELECT * FROM messages WHERE expediteur_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$outbox = $stmt->fetchAll();

// Pour chaque message envoyé, récupérer le nom du destinataire séparément
foreach ($outbox as &$msg) {
    $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
    $stmt->execute([$msg['destinataire_id']]);
    $user = $stmt->fetch();
    $msg['destinataire_nom'] = $user['nom'] ?? 'Inconnu';
}

// Récupération de la liste des utilisateurs possibles comme destinataires
// (sauf soi-même, utilisateurs actifs, et limités à admin/manager/employe)
$stmt = $pdo->prepare("SELECT id, nom, role FROM users WHERE id != ? AND actif = 1 AND role IN ('admin','manager','employe') ORDER BY nom");
$stmt->execute([$user_id]);
$users = $stmt->fetchAll();

// Configuration du titre de la page
$page_title = 'Messages - Production';
$module_color = 'primary';

// Inclusion de l'en-tête commun
require_once '../includes/header.php';
?>

<!-- Titre de la page -->
<h2 class="mb-4"><i class="bi bi-envelope"></i> Messages Production</h2>

<div class="row g-4">
    <!-- Colonne gauche: Formulaire d'envoi de message -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white"><i class="bi bi-send"></i> Nouveau message</div>
            <div class="card-body">
                <form method="post" action="../messages_process.php">
                    <!-- Champ caché pour la redirection après envoi -->
                    <input type="hidden" name="redirect" value="production/messages.php">

                    <!-- Sélection du destinataire -->
                    <div class="mb-3">
                        <label class="form-label">Destinataire</label>
                        <select name="destinataire_id" class="form-select" required>
                            <option value="">— Choisir —</option>
                            <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nom']) ?> (<?= $u['role'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Zone de message -->
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="contenu" class="form-control" rows="4" required></textarea>
                    </div>

                    <!-- Bouton d'envoi -->
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send"></i> Envoyer</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Colonne droite: Onglets messages reçus/envoyés -->
    <div class="col-md-8">
        <!-- Navigation par onglets -->
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#inbox">Reçus
                    (<?= count($inbox) ?>)</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#outbox">Envoyés
                    (<?= count($outbox) ?>)</a></li>
        </ul>

        <!-- Contenu des onglets -->
        <div class="tab-content">
            <!-- Messages reçus -->
            <div class="tab-pane fade show active" id="inbox">
                <?php if (!$inbox): ?><p class="text-muted">Aucun message.</p><?php endif; ?>
                <?php foreach ($inbox as $msg): ?>
                <div class="card mb-2">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between">
                            <strong><?= htmlspecialchars($msg['expediteur_nom']) ?></strong>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></small>
                        </div>
                        <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($msg['contenu'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Messages envoyés -->
            <div class="tab-pane fade" id="outbox">
                <?php if (!$outbox): ?><p class="text-muted">Aucun message envoyé.</p><?php endif; ?>
                <?php foreach ($outbox as $msg): ?>
                <div class="card mb-2">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between">
                            <span>À: <strong><?= htmlspecialchars($msg['destinataire_nom']) ?></strong></span>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></small>
                        </div>
                        <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($msg['contenu'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php 
// Inclusion du pied de page
require_once '../includes/footer.php'; 
?>