<?php
session_start();
$allowed_roles = ['admin'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';


$users = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();

$page_title   = 'Gestion des utilisateurs';
$module_color = 'dark';
require_once '../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> Utilisateurs</h2>
    <a href="user_add.php" class="btn btn-primary"><i class="bi bi-person-plus"></i> Ajouter</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Inscrit le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['nom']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php
                    $badge_colors = ['admin'=>'dark','manager'=>'primary','employe'=>'success','client'=>'info'];
                    $bc = $badge_colors[$u['role']] ?? 'secondary';
                    ?>
                        <span class="badge bg-<?= $bc ?>"><?= $u['role'] ?></span>
                    </td>
                    <td>
                        <?php if ($u['actif']): ?>
                        <span class="badge bg-success">Actif</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">Inactif</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <a href="user_edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                        <a href="user_deactivate.php?id=<?= $u['id'] ?>"
                            class="btn btn-sm btn-outline-<?= $u['actif'] ? 'warning' : 'success' ?>"
                            onclick="return confirm('Confirmer ?')">
                            <i class="bi bi-<?= $u['actif'] ? 'toggle-on' : 'toggle-off' ?>"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>