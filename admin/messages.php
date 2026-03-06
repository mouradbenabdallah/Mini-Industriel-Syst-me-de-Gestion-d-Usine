<?php
// Messages admin - boîte de réception et envoi

session_start();
$allowed_roles = ['admin'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$user_id = $_SESSION['user']['id'];

// Marquer les messages reçus comme lus
$pdo->prepare('UPDATE messages SET lu = 1 WHERE destinataire_id = ?')->execute([$user_id]);

// Récupérer les messages reçus (inbox) - sans JOIN
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

// Récupérer les messages envoyés (outbox) - sans JOIN
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

// Tous les utilisateurs pour sélectionner un destinataire (sauf soi-même)
$stmt = $pdo->prepare('SELECT id, nom, role FROM users WHERE id != ? AND actif = 1 ORDER BY nom');
$stmt->execute([$user_id]);
$users = $stmt->fetchAll();

$page_title = 'Messages Admin';
$module_color = 'dark';
require_once '../includes/header.php';
?>

<h2 class="mb-4"><i class="bi bi-envelope"></i> Messages</h2>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-teal text-white"><i class="bi bi-send"></i> Nouveau message</div>
            <div class="card-body">
                <form method="post" action="../messages_process.php">
                    <input type="hidden" name="redirect" value="admin/messages.php">
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
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="contenu" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-teal w-100"><i class="bi bi-send"></i> Envoyer</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <ul class="nav nav-tabs mb-3" id="msgTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#inbox">
                    <i class="bi bi-inbox"></i> Reçus (<?= count($inbox) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#outbox">
                    <i class="bi bi-send"></i> Envoyés (<?= count($outbox) ?>)
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="inbox">
                <?php if (!$inbox): ?>
                <p class="text-muted">Aucun message reçu.</p>
                <?php endif; ?>
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
            <div class="tab-pane fade" id="outbox">
                <?php if (!$outbox): ?>
                <p class="text-muted">Aucun message envoyé.</p>
                <?php endif; ?>
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

<?php require_once '../includes/footer.php'; ?>