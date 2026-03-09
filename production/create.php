<?php
// Créer un ordre de production

session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Récupérer la liste des employés
$stmt = $pdo->query('SELECT * FROM employes ORDER BY id');
$employes_list = $stmt->fetchAll();

// Pour chaque employé, récupérer son nom séparément
$employes = [];
foreach ($employes_list as $e) {
    $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ? AND actif = 1');
    $stmt->execute([$e['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $employes[] = [
            'id' => $e['id'],
            'nom' => $user['nom']
        ];
    }
}

$page_title = 'Créer un ordre de production';
$module_color = 'primary';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> Créer un ordre de production</h2>
    <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="create_process.php">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Assigner à l'employé</label>
                    <select name="employe_id" class="form-select">
                        <option value="">— Non assigné —</option>
                        <?php foreach ($employes as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Créer</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>