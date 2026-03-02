<?php
// Dashboard Admin - Version simplifiée pour débutants

session_start();
$allowed_roles = ['admin'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Compter les utilisateurs actifs - façon simple
$users = $pdo->query('SELECT id FROM users WHERE actif = 1')->fetchAll();
$users_count = count($users);

// Compter les produits - façon simple
$produits = $pdo->query('SELECT id FROM produits')->fetchAll();
$products_count = count($produits);

// Compter les commandes en attente - façon simple
$commandes_attente = $pdo->query("SELECT id FROM commandes WHERE statut = 'en_attente'")->fetchAll();
$pending_orders = count($commandes_attente);

// Compter les produits en rupture de stock - façon simple
$stock_bas = $pdo->query('SELECT id FROM produits WHERE quantite <= quantite_min')->fetchAll();
$low_stock = count($stock_bas);

// Calculer le revenue du mois en cours - façon simple
$commandes_mois = $pdo->query("SELECT total, created_at FROM commandes WHERE statut = 'livree'")->fetchAll();
$monthly_revenue = 0;
$current_month = date('Y-m');
foreach ($commandes_mois as $c) {
    $mois_commande = date('Y-m', strtotime($c['created_at']));
    if ($mois_commande === $current_month) {
        $monthly_revenue += $c['total'];
    }
}

// Compter les employés - façon simple
$employes = $pdo->query('SELECT id FROM employes')->fetchAll();
$employes_count = count($employes);

// Compter les congés en attente - façon simple
$conges_attente = $pdo->query("SELECT id FROM conges WHERE statut = 'en_attente'")->fetchAll();
$conges_count = count($conges_attente);

// Compter les ordres de production - façon simple
$ordres = $pdo->query('SELECT id FROM ordres')->fetchAll();
$ordres_count = count($ordres);

// Dernières commandes - façon simple
$Dernieres_commandes = $pdo->query('SELECT * FROM commandes ORDER BY created_at DESC LIMIT 5')->fetchAll();

// Pour chaque commande, récupérer le nom du client et du produit
foreach ($Dernieres_commandes as &$cmd) {
    $client = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
    $client->execute([$cmd['client_id']]);
    $c = $client->fetch();
    $cmd['client_nom'] = $c['nom'] ?? 'Inconnu';
    
    $produit = $pdo->prepare('SELECT nom FROM produits WHERE id = ?');
    $produit->execute([$cmd['produit_id']]);
    $p = $produit->fetch();
    $cmd['produit_nom'] = $p['nom'] ?? 'Inconnu';
}

$page_title = 'Dashboard Admin';
$module_color = 'dark';
require_once '../includes/header.php';
?>

<h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Admin</h2>

<!-- Cartes de statistiques -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-people text-primary fs-1"></i>
                <h4 class="mt-2"><?= $users_count ?></h4>
                <small class="text-muted">Utilisateurs actifs</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-box text-warning fs-1"></i>
                <h4 class="mt-2"><?= $products_count ?></h4>
                <small class="text-muted">Produits</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-cart text-danger fs-1"></i>
                <h4 class="mt-2"><?= $pending_orders ?></h4>
                <small class="text-muted">Commandes en attente</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                <h4 class="mt-2"><?= $low_stock ?></h4>
                <small class="text-muted">Stock bas</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-currency-dollar text-success fs-1"></i>
                <h4 class="mt-2"><?= number_format($monthly_revenue, 0, ',', ' ') ?></h4>
                <small class="text-muted">Revenus ce mois</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-people text-purple fs-1"></i>
                <h4 class="mt-2"><?= $employes_count ?></h4>
                <small class="text-muted">Employés</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Dernières commandes -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history"></i> Dernières commandes</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Produit</th>
                            <th>Total</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($Dernieres_commandes as $cmd): ?>
                        <tr>
                            <td>#<?= $cmd['id'] ?></td>
                            <td><?= htmlspecialchars($cmd['client_nom']) ?></td>
                            <td><?= htmlspecialchars($cmd['produit_nom']) ?></td>
                            <td><?= number_format($cmd['total'], 2, ',', ' ') ?> TND</td>
                            <td>
                                <span class="badge badge-<?= $cmd['statut'] ?>">
                                    <?= $cmd['statut'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (!$Dernieres_commandes): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Aucune commande</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                <a href="../ventes/commandes.php" class="btn btn-outline-dark btn-sm">Voir toutes les commandes</a>
            </div>
        </div>
    </div>

    <!-- Accès rapide -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-lightning"></i> Accès rapide</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="../admin/users.php" class="btn btn-outline-primary"><i class="bi bi-people"></i>
                        Utilisateurs</a>
                    <a href="../stock/index.php" class="btn btn-outline-warning"><i class="bi bi-box"></i> Stock</a>
                    <a href="../production/index.php" class="btn btn-outline-info"><i class="bi bi-wrench"></i>
                        Production</a>
                    <a href="../rh/employes.php" class="btn btn-outline-purple"><i class="bi bi-person-badge"></i>
                        Employés</a>
                    <a href="../rh/conges.php" class="btn btn-outline-secondary"><i class="bi bi-calendar"></i>
                        Congés</a>
                </div>
            </div>
        </div>

        <?php if ($conges_count > 0): ?>
        <div class="card border-warning">
            <div class="card-body">
                <i class="bi bi-exclamation-circle text-warning fs-4"></i>
                <strong><?= $conges_count ?></strong> congés en attente de validation
                <a href="../rh/conges.php" class="btn btn-sm btn-warning mt-2">Voir</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>