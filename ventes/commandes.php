<?php
// Liste des commandes

session_start();
$allowed_roles = ['admin','manager','client'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];

if (in_array($role, ['admin','manager'])) {
    // Admin/manager voit toutes les commandes
    $stmt = $pdo->query('SELECT * FROM commandes ORDER BY created_at DESC');
    $commandes_list = $stmt->fetchAll();
} else {
    // Client voit ses propres commandes
    $stmt = $pdo->prepare('SELECT * FROM commandes WHERE client_id = ? ORDER BY created_at DESC');
    $stmt->execute([$user_id]);
    $commandes_list = $stmt->fetchAll();
}

// Pour chaque commande, récupérer le nom du client et du produit séparément
$commandes = [];
foreach ($commandes_list as $c) {
    // Nom du client
    $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
    $stmt->execute([$c['client_id']]);
    $client = $stmt->fetch();
    
    // Nom du produit
    $stmt = $pdo->prepare('SELECT nom FROM produits WHERE id = ?');
    $stmt->execute([$c['produit_id']]);
    $produit = $stmt->fetch();
    
    $commandes[] = [
        'id' => $c['id'],
        'client_nom' => $client ? $client['nom'] : '',
        'produit_nom' => $produit ? $produit['nom'] : '',
        'quantite' => $c['quantite'],
        'total' => $c['total'],
        'statut' => $c['statut'],
        'created_at' => $c['created_at']
    ];
}

$page_title = 'Commandes';
$module_color = 'danger';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cart"></i> Commandes</h2>
    <?php if ($role === 'client'): ?>
    <a href="commande_add.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouvelle commande</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-danger">
                <tr>
                    <th>#</th>
                    <?php if (in_array($role, ['admin','manager'])): ?><th>Client</th><?php endif; ?>
                    <th>Produit</th>
                    <th>Qté</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $c): ?>
                <tr>
                    <td>#<?= $c['id'] ?></td>
                    <?php if (in_array($role, ['admin','manager'])): ?><td><?= htmlspecialchars($c['client_nom']) ?>
                    </td><?php endif; ?>
                    <td><?= htmlspecialchars($c['produit_nom']) ?></td>
                    <td><?= $c['quantite'] ?></td>
                    <td><?= number_format($c['total'], 2, ',', ' ') ?> TND</td>
                    <td><span class="badge badge-<?= $c['statut'] ?>"><?= $c['statut'] ?></span></td>
                    <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                    <td>
                        <a href="commande_view.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-secondary"><i
                                class="bi bi-eye"></i></a>
                        <?php if (in_array($role, ['admin','manager'])): ?>
                        <a href="commande_edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-warning"><i
                                class="bi bi-pencil"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$commandes): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">Aucune commande.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>