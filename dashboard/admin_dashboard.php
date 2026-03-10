<?php
/**
 * dashboard/admin_dashboard.php
 * Tableau de bord administrateur
 */

session_start();

$allowed_roles = ['admin'];

require_once '../config/database.php';
require_once '../includes/auth_check.php';

// ── STATISTIQUES ──────────────────────────────────────────────────────────────
$users_count    = $pdo->query('SELECT COUNT(*) FROM users WHERE actif = 1')->fetchColumn();
$products_count = $pdo->query('SELECT COUNT(*) FROM produits')->fetchColumn();
$pending_orders = $pdo->query("SELECT COUNT(*) FROM commandes WHERE statut = 'en_attente'")->fetchColumn();
$low_stock      = $pdo->query('SELECT COUNT(*) FROM produits WHERE quantite <= quantite_min')->fetchColumn();
$employes_count = $pdo->query('SELECT COUNT(*) FROM employes')->fetchColumn();
$conges_count   = $pdo->query("SELECT COUNT(*) FROM conges WHERE statut = 'en_attente'")->fetchColumn();

// ── REVENU DU MOIS ────────────────────────────────────────────────────────────
// ── REVENU DU MOIS ────────────────────────────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(total), 0)
    FROM commandes
    WHERE statut = 'livree'
      AND DATE_FORMAT(created_at, '%Y-%m') = ?
");
$stmt->execute([date('Y-m')]);
$monthly_revenue = $stmt->fetchColumn();

// ── DERNIÈRES COMMANDES ───────────────────────────────────────────────────────
$stmt = $pdo->query("
    SELECT id, total, statut, created_at, client_id, produit_id
    FROM commandes
    ORDER BY created_at DESC
    LIMIT 5
");
$dernieres_commandes = [];

foreach ($stmt->fetchAll() as $cmd) {
    // Récupérer le nom du client
    $client_nom  = 'Inconnu';
    $produit_nom = 'Inconnu';

    if ($cmd['client_id']) {
        $s = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
        $s->execute([$cmd['client_id']]);
        $client_nom = $s->fetchColumn() ?: 'Inconnu';
    }

    if ($cmd['produit_id']) {
        $s = $pdo->prepare('SELECT nom FROM produits WHERE id = ?');
        $s->execute([$cmd['produit_id']]);
        $produit_nom = $s->fetchColumn() ?: 'Inconnu';
    }

    $dernieres_commandes[] = [...$cmd, 'client_nom' => $client_nom, 'produit_nom' => $produit_nom];
}

// ── CONFIG PAGE ───────────────────────────────────────────────────────────────
$page_title   = 'Dashboard Admin';
$module_color = 'dark';

require_once '../includes/header.php';
?>

<!-- EN-TÊTE -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</h4>
        <small class="text-muted"><?= date('d F Y') ?></small>
    </div>
    <a href="../admin/statistics.php" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-bar-chart-line me-1"></i>Statistiques détaillées
    </a>
</div>

<!-- STATISTIQUES -->
<div class="row g-3 mb-4">

    <?php
    // Tableau des cartes à afficher — facile à modifier ou étendre
    $stats = [
        ['icon' => 'bi-people-fill',            'color' => 'primary', 'value' => $users_count,                                  'label' => 'Utilisateurs actifs'],
        ['icon' => 'bi-box-seam-fill',          'color' => 'warning', 'value' => $products_count,                               'label' => 'Produits'],
        ['icon' => 'bi-cart-fill',              'color' => 'danger',  'value' => $pending_orders,                               'label' => 'Commandes en attente'],
        ['icon' => 'bi-exclamation-triangle-fill','color'=> 'warning', 'value' => $low_stock,                                   'label' => 'Stock bas'],
        ['icon' => 'bi-cash-stack',             'color' => 'success', 'value' => number_format($monthly_revenue, 0, ',', ' '),  'label' => 'Revenus (TND)'],
        ['icon' => 'bi-person-badge-fill',      'color' => 'info',    'value' => $employes_count,                               'label' => 'Employés'],
    ];
    foreach ($stats as $s): ?>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body py-4">
                <div class="d-inline-flex align-items-center justify-content-center
                            bg-<?= $s['color'] ?>-subtle rounded-3 mb-3" style="width:50px;height:50px;">
                    <i class="bi <?= $s['icon'] ?> text-<?= $s['color'] ?> fs-4"></i>
                </div>
                <h4 class="fw-bold mb-0"><?= $s['value'] ?></h4>
                <small class="text-muted"><?= $s['label'] ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

</div>

<!-- TABLEAU + ACCÈS RAPIDE -->
<div class="row g-4">

    <!-- DERNIÈRES COMMANDES -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Dernières commandes</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Produit</th>
                            <th>Total</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($dernieres_commandes): ?>
                        <?php foreach ($dernieres_commandes as $cmd): ?>
                        <tr>
                            <td class="text-muted">#<?= $cmd['id'] ?></td>
                            <td><?= htmlspecialchars($cmd['client_nom']  ?? 'Inconnu') ?></td>
                            <td><?= htmlspecialchars($cmd['produit_nom'] ?? 'Inconnu') ?></td>
                            <td class="fw-semibold"><?= number_format($cmd['total'], 2, ',', ' ') ?> TND</td>
                            <td>
                                <?php
                                    // Badge couleur selon statut
                                    $badge = match($cmd['statut']) {
                                        'livree'     => 'success',
                                        'en_attente' => 'warning',
                                        'annulee'    => 'danger',
                                        default      => 'secondary',
                                    };
                                    ?>
                                <span class="badge bg-<?= $badge ?>"><?= $cmd['statut'] ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox me-2"></i>Aucune commande pour l'instant
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                <a href="../ventes/commandes.php" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-right me-1"></i>Voir toutes les commandes
                </a>
            </div>
        </div>
    </div>

    <!-- ACCÈS RAPIDE + ALERTES -->
    <div class="col-md-4 d-flex flex-column gap-3">

        <!-- Raccourcis modules -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-lightning-fill me-2 text-warning"></i>Accès rapide</h6>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="../admin/users.php" class="btn btn-outline-primary   btn-sm"><i
                        class="bi bi-people me-2"></i>Utilisateurs</a>
                <a href="../stock/index.php" class="btn btn-outline-warning   btn-sm"><i
                        class="bi bi-box-seam me-2"></i>Stock</a>
                <a href="../production/index.php" class="btn btn-outline-info      btn-sm"><i
                        class="bi bi-hammer me-2"></i>Production</a>
                <a href="../rh/employes.php" class="btn btn-outline-secondary btn-sm"><i
                        class="bi bi-person-badge me-2"></i>Employés</a>
                <a href="../rh/conges.php" class="btn btn-outline-secondary btn-sm"><i
                        class="bi bi-calendar-check me-2"></i>Congés</a>
            </div>
        </div>

        <!-- Alerte congés en attente -->
        <?php if ($conges_count > 0): ?>
        <div class="card border-0 shadow-sm border-start border-warning border-3">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <i class="bi bi-exclamation-circle-fill text-warning fs-3"></i>
                    <div>
                        <div class="fw-semibold"><?= $conges_count ?> congé<?= $conges_count > 1 ? 's' : '' ?> en
                            attente</div>
                        <small class="text-muted">Des employés attendent votre validation</small>
                    </div>
                </div>
                <a href="../rh/conges.php" class="btn btn-warning btn-sm w-100">
                    <i class="bi bi-calendar-check me-1"></i>Gérer les congés
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Alerte stock bas -->
        <?php if ($low_stock > 0): ?>
        <div class="card border-0 shadow-sm border-start border-danger border-3">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <i class="bi bi-box-seam text-danger fs-3"></i>
                    <div>
                        <div class="fw-semibold"><?= $low_stock ?> produit<?= $low_stock > 1 ? 's' : '' ?> en stock bas
                        </div>
                        <small class="text-muted">Le stock doit être réapprovisionné</small>
                    </div>
                </div>
                <a href="../stock/index.php" class="btn btn-danger btn-sm w-100">
                    <i class="bi bi-eye me-1"></i>Voir le stock
                </a>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>