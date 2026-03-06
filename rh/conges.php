<?php
// Liste des demandes de congés

session_start();
$allowed_roles = ['admin','manager','employe'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];

if (in_array($role, ['admin','manager'])) {
    // Admin/manager voit toutes les demandes
    $stmt = $pdo->query('SELECT * FROM conges ORDER BY created_at DESC');
    $conges_list = $stmt->fetchAll();
} else {
    // Employé: voir ses propres demandes
    $stmt = $pdo->prepare('SELECT id FROM employes WHERE user_id=?');
    $stmt->execute([$user_id]);
    $emp = $stmt->fetch();
    $conges_list = [];
    if ($emp) {
        $stmt = $pdo->prepare('SELECT * FROM conges WHERE employe_id=? ORDER BY created_at DESC');
        $stmt->execute([$emp['id']]);
        $conges_list = $stmt->fetchAll();
    }
}

// Pour chaque congé, récupérer le nom de l'employé séparément
$conges = [];
foreach ($conges_list as $c) {
    $stmt = $pdo->prepare('SELECT user_id FROM employes WHERE id = ?');
    $stmt->execute([$c['employe_id']]);
    $emp = $stmt->fetch();
    if ($emp) {
        $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
        $stmt->execute([$emp['user_id']]);
        $user = $stmt->fetch();
        $conges[] = [
            'id' => $c['id'],
            'employe_nom' => $user ? $user['nom'] : '',
            'date_debut' => $c['date_debut'],
            'date_fin' => $c['date_fin'],
            'motif' => $c['motif'],
            'statut' => $c['statut'],
            'created_at' => $c['created_at']
        ];
    }
}

$page_title = 'Demandes de congés';
$module_color = 'success';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-calendar"></i> Demandes de congés</h2>
    <?php if ($role === 'employe'): ?>
    <a href="conge_add.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-success">
                <tr>
                    <th>Employé</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conges as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['employe_nom']) ?></td>
                    <td><?= date('d/m/Y', strtotime($c['date_debut'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($c['date_fin'])) ?></td>
                    <td><?= htmlspecialchars($c['motif'] ?? '-') ?></td>
                    <td><span class="badge badge-<?= $c['statut'] ?>"><?= $c['statut'] ?></span></td>
                    <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                    <td>
                        <?php if (in_array($role, ['admin','manager']) && $c['statut'] === 'en_attente'): ?>
                        <a href="conge_approve.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-success"><i
                                class="bi bi-check"></i></a>
                        <a href="conge_reject.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger"><i
                                class="bi bi-x"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$conges): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">Aucune demande de congé.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>