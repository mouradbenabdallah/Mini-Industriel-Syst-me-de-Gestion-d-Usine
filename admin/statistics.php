<?php
// Statistiques simples pour l'admin - Version simplifiée pour débutants

session_start();
$allowed_roles = ['admin'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Compter les utilisateurs - façon simple
$all_users = $pdo->query('SELECT id FROM users')->fetchAll();
$stats['users_total'] = count($all_users);

// Compter les utilisateurs actifs
$actif_users = $pdo->query('SELECT id FROM users WHERE actif = 1')->fetchAll();
$stats['users_actif'] = count($actif_users);

// Compter les produits
$all_produits = $pdo->query('SELECT id FROM produits')->fetchAll();
$stats['produits'] = count($all_produits);

// Compter les produits en rupture de stock (quantité <= quantité minimum)
$low_stock_produits = $pdo->query('SELECT id FROM produits WHERE quantite <= quantite_min')->fetchAll();
$stats['low_stock'] = count($low_stock_produits);

// Compter les ordres
$all_ordres = $pdo->query('SELECT id FROM ordres')->fetchAll();
$stats['ordres_total'] = count($all_ordres);

// Compter les ordres terminés
$termine_ordres = $pdo->query("SELECT id FROM ordres WHERE statut = 'termine'")->fetchAll();
$stats['ordres_termine'] = count($termine_ordres);

// Compter les commandes
$all_commandes = $pdo->query('SELECT id FROM commandes')->fetchAll();
$stats['commandes_total'] = count($all_commandes);

// Calculer le revenue total (commandes livrées) - façon simple
$livrees = $pdo->query("SELECT total FROM commandes WHERE statut = 'livree'")->fetchAll();
$revenue_total = 0;
foreach ($livrees as $c) {
    $revenue_total += $c['total'];
}
$stats['revenue_total'] = $revenue_total;

// Compter les employés
$all_employes = $pdo->query('SELECT id FROM employes')->fetchAll();
$stats['employes'] = count($all_employes);

// Compter les congés en attente
$pending_conges = $pdo->query("SELECT id FROM conges WHERE statut = 'en_attente'")->fetchAll();
$stats['conges_pending'] = count($pending_conges);

// Revenus mensuels (6 derniers mois) - façon simple
$monthly = [];
$commandes_all = $pdo->query("SELECT total, created_at FROM commandes WHERE statut = 'livree' ORDER BY created_at DESC")->fetchAll();

// Grouper par mois en PHP
$monthly_data = [];
foreach ($commandes_all as $c) {
    $mois = date('Y-m', strtotime($c['created_at']));
    if (!isset($monthly_data[$mois])) {
        $monthly_data[$mois] = 0;
    }
    $monthly_data[$mois] += $c['total'];
}

// Prendre les 6 premiers mois
$monthly = [];
$i = 0;
foreach ($monthly_data as $mois => $total) {
    if ($i < 6) {
        $monthly[] = ['mois' => $mois, 'total' => $total];
        $i++;
    }
}

// Répartition des rôles - façon simple
$all_users_for_role = $pdo->query('SELECT role FROM users')->fetchAll();
$roles_count = [];
foreach ($all_users_for_role as $u) {
    $role = $u['role'];
    if (!isset($roles_count[$role])) {
        $roles_count[$role] = 0;
    }
    $roles_count[$role]++;
}
$roles = [];
foreach ($roles_count as $role => $cnt) {
    $roles[] = ['role' => $role, 'cnt' => $cnt];
}

$page_title = 'Statistiques';
$module_color = 'dark';
require_once '../includes/header.php';
?>

<h2 class="mb-4"><i class="bi bi-bar-chart"></i> Statistiques globales</h2>

<div class="row g-3 mb-4">
    <?php
    $stat_items = [
        ['label'=>'Utilisateurs (total)', 'value'=>$stats['users_total'], 'icon'=>'people', 'color'=>'primary'],
        ['label'=>'Utilisateurs actifs', 'value'=>$stats['users_actif'], 'icon'=>'person-check', 'color'=>'success'],
        ['label'=>'Produits', 'value'=>$stats['produits'], 'icon'=>'box-seam', 'color'=>'warning'],
        ['label'=>'Alertes stock bas', 'value'=>$stats['low_stock'], 'icon'=>'exclamation-triangle', 'color'=>'danger'],
        ['label'=>'Ordres (total)', 'value'=>$stats['ordres_total'], 'icon'=>'wrench', 'color'=>'info'],
        ['label'=>'Ordres terminés', 'value'=>$stats['ordres_termine'], 'icon'=>'check-circle', 'color'=>'success'],
        ['label'=>'Commandes (total)', 'value'=>$stats['commandes_total'], 'icon'=>'cart', 'color'=>'danger'],
        ['label'=>'CA total (livré)', 'value'=>number_format($stats['revenue_total'],2,',',' ').' TND', 'icon'=>'currency-dollar', 'color'=>'success'],
        ['label'=>'Employés', 'value'=>$stats['employes'], 'icon'=>'person-badge', 'color'=>'purple'],
        ['label'=>'Congés en attente', 'value'=>$stats['conges_pending'], 'icon'=>'calendar-x', 'color'=>'warning'],
    ];
    foreach ($stat_items as $si):
    ?>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card border-<?= $si['color'] ?> h-100">
            <div class="card-body text-center py-3">
                <i class="bi bi-<?= $si['icon'] ?> text-<?= $si['color'] ?>"></i>
                <h4 class="mt-1 mb-0"><?= $si['value'] ?></h4>
                <small class="text-muted"><?= $si['label'] ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar3"></i> CA mensuel (6 derniers mois)</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Mois</th>
                            <th>CA (livré)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monthly as $m): ?>
                        <tr>
                            <td><?= $m['mois'] ?></td>
                            <td class="fw-bold text-success"><?= number_format($m['total'],2,',',' ') ?> TND</td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (!$monthly): ?><tr>
                            <td colspan="2" class="text-center text-muted">Aucune donnée</td>
                        </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-pie-chart"></i> Répartition des rôles</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Rôle</th>
                            <th>Nombre</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $r): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?= $r['role'] ?></span></td>
                            <td><?= $r['cnt'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>