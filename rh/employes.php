<?php
// Liste des employés

session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Récupérer tous les employés
$stmt = $pdo->query('SELECT * FROM employes ORDER BY id');
$employes_list = $stmt->fetchAll();

// Pour chaque employé, récupérer les informations utilisateur séparément
$employes = [];
foreach ($employes_list as $e) {
    $stmt = $pdo->prepare('SELECT nom, email, actif FROM users WHERE id = ?');
    $stmt->execute([$e['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $employes[] = [
            'id' => $e['id'],
            'user_id' => $e['user_id'],
            'nom' => $user['nom'],
            'email' => $user['email'],
            'actif' => $user['actif'],
            'poste' => $e['poste'],
            'salaire_base' => $e['salaire_base'],
            'telephone' => $e['telephone'] ?? ''
        ];
    }
}

$page_title = 'Employés';
$module_color = 'success';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> Liste des employés</h2>
    <a href="employe_add.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Ajouter un employé</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-success">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Poste</th>
                    <th>Salaire</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employes as $e): ?>
                <tr>
                    <td><?= htmlspecialchars($e['nom']) ?></td>
                    <td><?= htmlspecialchars($e['email']) ?></td>
                    <td><?= htmlspecialchars($e['poste']) ?></td>
                    <td><?= number_format($e['salaire_base'], 2, ',', ' ') ?> TND</td>
                    <td><?= htmlspecialchars($e['telephone'] ?? '-') ?></td>
                    <td><span
                            class="badge bg-<?= $e['actif'] ? 'success' : 'secondary' ?>"><?= $e['actif'] ? 'Actif' : 'Inactif' ?></span>
                    </td>
                    <td>
                        <a href="employe_edit.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-warning"><i
                                class="bi bi-pencil"></i></a>
                        <a href="employe_delete.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Supprimer cet employé ?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$employes): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">Aucun employé trouvé.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>