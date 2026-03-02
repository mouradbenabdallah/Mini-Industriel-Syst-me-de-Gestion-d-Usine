<?php
// Dashboard Manager - Version simplifiée pour débutants

session_start();
$allowed_roles = ['manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Compter les ordres en attente - façon simple
$ordres_attente = $pdo->query("SELECT id FROM ordres WHERE statut = 'en_attente'")->fetchAll();
$pending_ordres = count($ordres_attente);

// Compter les produits en rupture de stock - façon simple
$stock_bas = $pdo->query('SELECT id FROM produits WHERE quantite <= quantite_min')->fetchAll();
$low_stock = count($stock_bas);

// Compter les congés en attente - façon simple
$conges_attente = $pdo->query("SELECT id FROM conges WHERE statut = 'en_attente'")->fetchAll();
$pending_conges = count($conges_attente);

// Récupérer les 5 dernières commandes
$stmt = $pdo->query('SELECT * FROM commandes ORDER BY created_at DESC LIMIT 5');
$recent_commandes = $stmt->fetchAll();

// Pour chaque commande, récupérer le nom du client et du produit séparément
foreach ($recent_commandes as &$cmd) {
    // Nom du client
    $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
    $stmt->execute([$cmd['client_id']]);
    $client = $stmt->fetch();
    $cmd['client_nom'] = $client ? $client['nom'] : '';
    
    // Nom du produit
    $stmt = $pdo->prepare('SELECT nom FROM produits WHERE id = ?');
    $stmt->execute([$cmd['produit_id']]);
    $produit = $stmt->fetch();
    $cmd['produit_nom'] = $produit ? $produit['nom'] : '';
}

// Récupérer les produits en rupture de stock
$stmt = $pdo->query('SELECT * FROM produits WHERE quantite <= quantite_min ORDER BY quantite ASC LIMIT 5');
$low_stock_items = $stmt->fetchAll();

$page_title = 'Tableau de bord Manager';
$module_color = 'primary';
require_once '../includes/header.php';
?>

<h2 class="mb-4"><i class="bi bi-speedometer2"></i> Tableau de bord - Manager</h2>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card stat-card border-warning h-100">
            <div class="card-body text-center">
                <i class="bi bi-hourglass-split text-warning" style="font-size:2rem;"></i>
                <h3 class="mt-2"><?= $pending_ordres ?></h3>
                <p class="text-muted mb-0">Ordres en attente</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card border-danger h-100">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle text-danger" style="font-size:2rem;"></i>
                <h3 class="mt-2"><?= $low_stock ?></h3>
                <p class="text-muted mb-0">Alertes stock bas</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card border-info h-100">
            <div class="card-body text-center">
                <i class="bi bi-calendar-check text-info" style="font-size:2rem;"></i>
                <h3 class="mt-2"><?= $pending_conges ?></h3>
                <p class="text-muted mb-0">Congés en attente</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-cart-check"></i> Dernières commandes</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Produit</th>
                            <th>Statut</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_commandes as $cmd): ?>
                        <tr>
                            <td><?= htmlspecialchars($cmd['client_nom']) ?></td>
                            <td><?= htmlspecialchars($cmd['produit_nom']) ?></td>
                            <td><span class="badge badge-<?= $cmd['statut'] ?>"><?= $cmd['statut'] ?></span></td>
                            <td><?= number_format($cmd['total'], 2, ',', ' ') ?> TND</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="../ventes/commandes.php" class="btn btn-sm btn-primary">Voir toutes</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-danger"><i class="bi bi-exclamation-triangle"></i> Stock bas</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Qté</th>
                            <th>Min.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($low_stock_items as $p): ?>
                        <tr class="table-warning">
                            <td><?= htmlspecialchars($p['nom']) ?></td>
                            <td><?= $p['quantite'] ?></td>
                            <td><?= $p['quantite_min'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (!$low_stock_items): ?><tr>
                            <td colspan="3" class="text-center text-muted">Aucune alerte</td>
                        </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="../stock/index.php" class="btn btn-sm btn-warning">Gérer le stock</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>