<?php
// Liste des ordres de production

session_start();
$allowed_roles = ['admin','manager','employe'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];

if (in_array($role, ['admin','manager'])) {
    // Admin/manager voit tous les ordres
    $stmt = $pdo->query('SELECT * FROM ordres ORDER BY created_at DESC');
    $ordres = $stmt->fetchAll();
} else {
    // Employé: chercher d'abord son ID employé
    $stmt = $pdo->prepare('SELECT id FROM employes WHERE user_id=?');
    $stmt->execute([$user_id]);
    $emp = $stmt->fetch();
    $ordres = [];
    if ($emp) {
        $stmt = $pdo->prepare('SELECT * FROM ordres WHERE employe_id=? ORDER BY created_at DESC');
        $stmt->execute([$emp['id']]);
        $ordres = $stmt->fetchAll();
    }
}

// Pour chaque ordre, récupérer le nom de l'employé et du manager séparément
foreach ($ordres as &$o) {
    // Nom de l'employé
    if ($o['employe_id']) {
        $stmt = $pdo->prepare('SELECT user_id FROM employes WHERE id = ?');
        $stmt->execute([$o['employe_id']]);
        $emp = $stmt->fetch();
        if ($emp) {
            $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
            $stmt->execute([$emp['user_id']]);
            $user = $stmt->fetch();
            $o['employe_nom'] = $user ? $user['nom'] : '';
        } else {
            $o['employe_nom'] = '';
        }
    } else {
        $o['employe_nom'] = '';
    }
    
    // Nom du manager
    if ($o['manager_id']) {
        $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
        $stmt->execute([$o['manager_id']]);
        $manager = $stmt->fetch();
        $o['manager_nom'] = $manager ? $manager['nom'] : '';
    } else {
        $o['manager_nom'] = '';
    }
}

$page_title = 'Ordres de production';
$module_color = 'primary';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-wrench"></i> Production</h2>
    <?php if (in_array($role, ['admin','manager'])): ?>
    <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Créer un ordre</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Titre</th>
                    <th>Statut</th>
                    <th>Employé</th>
                    <th>Manager</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordres as $o): ?>
                <tr>
                    <td><?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['titre']) ?></td>
                    <td><span class="badge badge-<?= $o['statut'] ?>"><?= $o['statut'] ?></span></td>
                    <td><?= htmlspecialchars($o['employe_nom'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($o['manager_nom'] ?? '-') ?></td>
                    <td><?= date('d/m/Y', strtotime($o['created_at'])) ?></td>
                    <td>
                        <a href="view.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <?php if (in_array($role, ['admin','manager'])): ?>
                        <a href="edit.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="delete.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Supprimer cet ordre ?')">
                            <i class="bi bi-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$ordres): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">Aucun ordre de production.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>