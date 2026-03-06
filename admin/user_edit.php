<?php
session_start();
$allowed_roles = ['admin'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';


$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = 'Utilisateur introuvable.';
    header('Location: users.php');
    exit;
}

$page_title   = 'Modifier utilisateur';
$module_color = 'dark';
require_once '../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil-square"></i> Modifier l'utilisateur <?= htmlspecialchars($user['nom']) ?></h2>
    <a href="users.php" class="btn btn-outline-dark"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="user_edit_process.php">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Nom complet</label>
                <input type="text" name="nom" class="form-control" required
                    value="<?= htmlspecialchars($user['nom']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required
                    value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Nouveau mot de passe <small class="text-muted">(laisser vide =
                        inchangé)</small></label>
                <input type="password" name="password" class="form-control" minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label">Rôle</label>
                <select name="role" class="form-select" <?= $user['id'] == $_SESSION['user']['id'] ? 'disabled' : '' ?>>
                    <?php foreach (['client','employe','manager','admin'] as $r): ?>
                    <option value="<?= $r ?>" <?= $user['role']===$r ? 'selected' : '' ?>><?= $r ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($user['id'] == $_SESSION['user']['id']): ?>
                <input type="hidden" name="role" value="<?= htmlspecialchars($user['role']) ?>">
                <?php endif; ?>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="actif" class="form-check-input" id="actif"
                    <?= $user['actif'] ? 'checked' : '' ?>
                    <?= $user['id'] == $_SESSION['user']['id'] ? 'disabled' : '' ?>>
                <label class="form-check-label" for="actif">Compte actif</label>
                <?php if ($user['id'] == $_SESSION['user']['id']): ?>
                <input type="hidden" name="actif" value="1">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-dark"><i class="bi bi-save"></i> Enregistrer</button>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>