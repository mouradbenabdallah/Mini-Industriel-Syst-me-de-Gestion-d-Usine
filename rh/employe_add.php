<?php
// Ajouter un nouvel employé

session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Récupérer les utilisateurs avec le rôle employe qui ne sont pas encore dans la table employes
$stmt = $pdo->query("SELECT id, nom, email FROM users WHERE role='employe' ORDER BY nom");
$users_list = $stmt->fetchAll();

// Filtrer pour ne garder que ceux qui ne sont pas déjà employés
$available = [];
foreach ($users_list as $u) {
    $stmt = $pdo->prepare('SELECT id FROM employes WHERE user_id = ?');
    $stmt->execute([$u['id']]);
    if (!$stmt->fetch()) {
        $available[] = $u;
    }
}

$page_title = 'Ajouter un employé';
$module_color = 'success';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-plus"></i> Ajouter un employé</h2>
    <a href="employes.php" class="btn btn-outline-success"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="employe_add_process.php">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Utilisateur</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($available as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nom']) ?>
                            (<?= htmlspecialchars($u['email']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Poste</label>
                    <input type="text" name="poste" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Salaire de base (TND)</label>
                    <input type="number" name="salaire_base" class="form-control" min="0" step="0.01" required
                        value="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date d'embauche</label>
                    <input type="date" name="date_embauche" class="form-control">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>