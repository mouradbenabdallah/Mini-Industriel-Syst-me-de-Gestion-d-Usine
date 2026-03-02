<?php
// Dashboard Client - Version simplifiée pour débutants

session_start();
$allowed_roles = ['client'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$user_id = $_SESSION['user']['id'];

// Récupérer toutes les commandes du client
$stmt = $pdo->prepare('SELECT * FROM commandes WHERE client_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$commandes = $stmt->fetchAll();

// Pour chaque commande, récupérer le nom du produit séparément
foreach ($commandes as &$c) {
    $stmt = $pdo->prepare('SELECT nom FROM produits WHERE id = ?');
    $stmt->execute([$c['produit_id']]);
    $produit = $stmt->fetch();
    $c['produit_nom'] = $produit ? $produit['nom'] : '';
}

// Calculer le total dépensé (commandes livrées) - façon simple
$total_spent = 0;
foreach ($commandes as $c) {
    if ($c['statut'] === 'livree') {
        $total_spent += $c['total'];
    }
}

$page_title = 'Mon espace client';
$module_color = 'danger';
require_once '../includes/header.php';
?>

<h2 class="mb-4"><i class="bi bi-person-circle"></i> Mon espace - Client</h2>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card border-primary text-center">
            <div class="card-body">
                <i class="bi bi-cart text-primary" style="font-size:2rem;"></i>
                <h3 class="mt-2"><?= count($commandes) ?></h3>
                <p class="text-muted mb-0">Total commandes</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card border-success text-center">
            <div class="card-body">
                <i class="bi bi-currency-dollar text-success" style="font-size:2rem;"></i>
                <h3 class="mt-2"><?= number_format($total_spent, 2, ',', ' ') ?> TND</h3>
                <p class="text-muted mb-0">Total dépensé (livré)</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 d-flex align-items-center">
        <a href="../ventes/commande_add.php" class="btn btn-danger btn-lg w-100">
            <i class="bi bi-cart-plus"></i> Nouvelle commande
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-list-ul"></i> Historique de mes commandes</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
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
                    <td><?= htmlspecialchars($c['produit_nom']) ?></td>
                    <td><?= $c['quantite'] ?></td>
                    <td><?= number_format($c['total'], 2, ',', ' ') ?> TND</td>
                    <td><span class="badge badge-<?= $c['statut'] ?>"><?= $c['statut'] ?></span></td>
                    <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                    <td>
                        <a href="../ventes/commande_view.php?id=<?= $c['id'] ?>"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$commandes): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">Aucune commande pour l'instant.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>