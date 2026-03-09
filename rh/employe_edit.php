<?php
// Modifier un employé

session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$id = (int)($_GET['id'] ?? 0);

// Récupérer l'employé
$stmt = $pdo->prepare('SELECT * FROM employes WHERE id = ?');
$stmt->execute([$id]);
$employe = $stmt->fetch();

if (!$employe) {
    $_SESSION['error'] = 'Employé introuvable.';
    header('Location: employes.php');
    exit;
}

// Récupérer les informations utilisateur séparément
$stmt = $pdo->prepare('SELECT nom, email FROM users WHERE id = ?');
$stmt->execute([$employe['user_id']]);
$user = $stmt->fetch();

$page_title = 'Modifier employé';
$module_color = 'success';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Modifier - <?= htmlspecialchars($user['nom'] ?? '') ?></h2>
    <a href="employes.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="employe_edit_process.php">
            <input type="hidden" name="id" value="<?= $employe['id'] ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nom</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['nom'] ?? '') ?>"
                        disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                        disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Poste</label>
                    <input type="text" name="poste" class="form-control" required
                        value="<?= htmlspecialchars($employe['poste']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Salaire de base (TND)</label>
                    <input type="number" name="salaire_base" class="form-control" min="0" step="0.01" required
                        value="<?= $employe['salaire_base'] ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control"
                        value="<?= htmlspecialchars($employe['telephone'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date d'embauche</label>
                    <input type="date" name="date_embauche" class="form-control"
                        value="<?= $employe['date_embauche'] ?>">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>