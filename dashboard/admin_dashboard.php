<?php
/**
 * ============================================================
 * FICHIER: dashboard/admin_dashboard.php
 * DESCRIPTION: Tableau de bord administrateur
 * PROJET: Usine Industriel - SystÃ¨me de gestion d'usine
 * ============================================================
 *
 * Ce tableau de bord affiche un rÃ©sumÃ© complet du systÃ¨me:
 *  - Compteurs (utilisateurs, produits, commandes, revenus, employÃ©s)
 *  - Tableau des derniÃ¨res commandes
 *  - Liens d'accÃ¨s rapide vers les modules
 *  - Alertes (congÃ©s en attente, stock bas)
 */

// DÃ©marrage de la session (obligatoire pour vÃ©rifier si l'utilisateur est connectÃ©)
session_start();

// Seuls les admins peuvent voir cette page
$allowed_roles = ['admin'];

// Inclusion de la base de donnÃ©es et de la vÃ©rification d'authentification
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// ============================================================
// REQUÃŠTES DE STATISTIQUES
// ============================================================
// On utilise COUNT(*) directement dans SQL pour compter les lignes.
// C'est plus efficace que de rÃ©cupÃ©rer toutes les lignes et compter en PHP.

// Nombre d'utilisateurs actifs (actif = 1 dans la base de donnÃ©es)
$stmt = $pdo->query('SELECT COUNT(*) FROM users WHERE actif = 1');
$users_count = $stmt->fetchColumn(); // fetchColumn() rÃ©cupÃ¨re juste la premiÃ¨re colonne (le nombre)

// Nombre total de produits dans le stock
$stmt = $pdo->query('SELECT COUNT(*) FROM produits');
$products_count = $stmt->fetchColumn();

// Nombre de commandes en attente d'Ãªtre traitÃ©es
$stmt = $pdo->query("SELECT COUNT(*) FROM commandes WHERE statut = 'en_attente'");
$pending_orders = $stmt->fetchColumn();

// Nombre de produits en rupture ou stock bas (quantitÃ© â‰¤ quantitÃ© minimum)
$stmt = $pdo->query('SELECT COUNT(*) FROM produits WHERE quantite <= quantite_min');
$low_stock = $stmt->fetchColumn();

// Nombre total d'employÃ©s dans la table des employÃ©s
$stmt = $pdo->query('SELECT COUNT(*) FROM employes');
$employes_count = $stmt->fetchColumn();

// Nombre de demandes de congÃ©s en attente de validation
$stmt = $pdo->query("SELECT COUNT(*) FROM conges WHERE statut = 'en_attente'");
$conges_count = $stmt->fetchColumn();

// ============================================================
// CALCUL DU REVENU DU MOIS COURANT
// ============================================================
// On rÃ©cupÃ¨re la somme (SUM) des totaux des commandes livrÃ©es ce mois-ci.
// DATE_FORMAT() en SQL extrait l'annÃ©e et le mois d'une date.
// CURDATE() retourne la date d'aujourd'hui dans SQL.
$moisActuel = date('Y-%m'); // Format: "2025-03"
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(total), 0) as revenu
    FROM commandes
    WHERE statut = 'livree'
      AND DATE_FORMAT(created_at, '%Y-%m') = ?
");
$stmt->execute([$moisActuel]);
$monthly_revenue = $stmt->fetchColumn(); // COALESCE retourne 0 si aucune commande ce mois

// ============================================================
// CORRECTION DU BUG N+1: DERNIÃˆRES COMMANDES AVEC JOIN
// ============================================================
// AVANT (problÃ¨me): On faisait 2 requÃªtes SQL supplÃ©mentaires pour CHAQUE ligne
//   â†’ 5 commandes = 1 + 5Ã—2 = 11 requÃªtes SQL ! (trÃ¨s lent)
//
// MAINTENANT (correct): Une seule requÃªte SQL avec des JOIN.
//   Un JOIN "joint" plusieurs tables en une seule requÃªte.
//   LEFT JOIN retourne la commande mÃªme si le client ou produit n'existe plus.
//
// SchÃ©ma de la requÃªte:
//   commandes â†’ LEFT JOIN â†’ users (pour le nom du client)
//            â†’ LEFT JOIN â†’ produits (pour le nom du produit)
$stmt = $pdo->query("
    SELECT
        c.id,
        c.total,
        c.statut,
        c.created_at,
        u.nom  AS client_nom,       -- Nom du client (depuis la table users)
        p.nom  AS produit_nom        -- Nom du produit (depuis la table produits)
    FROM commandes c
    LEFT JOIN users    u ON c.client_id  = u.id   -- Lier la commande Ã  son client
    LEFT JOIN produits p ON c.produit_id = p.id   -- Lier la commande Ã  son produit
    ORDER BY c.created_at DESC
    LIMIT 5                                        -- Seulement les 5 derniÃ¨res
");
$dernieres_commandes = $stmt->fetchAll();
// Maintenant on a TOUT en une seule requÃªte.
// client_nom et produit_nom sont directement dans chaque ligne du rÃ©sultat.

// ============================================================
// CONFIGURATION DE LA PAGE
// ============================================================
$page_title   = 'Dashboard Admin';    // Titre affichÃ© dans l'onglet et le breadcrumb
$module_color = 'dark';               // Couleur bleue foncÃ©e pour le module admin

// Inclusion de l'en-tÃªte commun (navbar, CSS, etc.)
require_once '../includes/header.php';
?>

<!-- ============================================================
     EN-TÃŠTE DE LA PAGE
     ============================================================ -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-0 fw-bold">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
        </h2>
        <p class="text-muted mb-0 small">Vue d'ensemble du systÃ¨me Â· <?= date('d F Y') ?></p>
    </div>
    <!-- Lien vers les statistiques dÃ©taillÃ©es -->
    <a href="../admin/statistics.php" class="btn btn-outline-primary">
        <i class="bi bi-bar-chart-line me-1"></i>Statistiques dÃ©taillÃ©es
    </a>
</div>

<!-- ============================================================
     CARTES DE STATISTIQUES
     Chaque carte affiche un chiffre clÃ© avec une icÃ´ne colorÃ©e.
     ============================================================ -->
<div class="row g-3 mb-4">

    <!-- Carte 1: Utilisateurs actifs -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card h-100 border-0 text-center">
            <div class="card-body py-4">
                <!-- IcÃ´ne dans un carrÃ© colorÃ© Bootstrap: bg-primary-subtle = fond bleu clair -->
                <div class="d-inline-flex align-items-center justify-content-center
                            bg-primary-subtle rounded-3 mb-3"
                     style="width:52px;height:52px;">
                    <i class="bi bi-people-fill text-primary fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0"><?= $users_count ?></h3>
                <small class="text-muted">Utilisateurs actifs</small>
            </div>
        </div>
    </div>

    <!-- Carte 2: Produits -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card h-100 border-0 text-center">
            <div class="card-body py-4">
                <div class="d-inline-flex align-items-center justify-content-center
                            bg-warning-subtle rounded-3 mb-3"
                     style="width:52px;height:52px;">
                    <i class="bi bi-box-seam-fill text-warning fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0"><?= $products_count ?></h3>
                <small class="text-muted">Produits</small>
            </div>
        </div>
    </div>

    <!-- Carte 3: Commandes en attente -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card h-100 border-0 text-center <?= $pending_orders > 0 ? 'border-start border-warning border-3' : '' ?>">
            <div class="card-body py-4">
                <div class="d-inline-flex align-items-center justify-content-center
                            bg-danger-subtle rounded-3 mb-3"
                     style="width:52px;height:52px;">
                    <i class="bi bi-cart-fill text-danger fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0"><?= $pending_orders ?></h3>
                <small class="text-muted">Commandes en attente</small>
            </div>
        </div>
    </div>

    <!-- Carte 4: Stock bas -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card h-100 border-0 text-center <?= $low_stock > 0 ? 'border-start border-danger border-3' : '' ?>">
            <div class="card-body py-4">
                <!-- bg-warning-subtle + text-warning pour imiter l'orange sans classe custom -->
                <div class="d-inline-flex align-items-center justify-content-center
                            bg-warning-subtle rounded-3 mb-3"
                     style="width:52px;height:52px;">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0 <?= $low_stock > 0 ? 'text-danger' : '' ?>"><?= $low_stock ?></h3>
                <small class="text-muted">Stock bas</small>
            </div>
        </div>
    </div>

    <!-- Carte 5: Revenus du mois -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card h-100 border-0 text-center">
            <div class="card-body py-4">
                <div class="d-inline-flex align-items-center justify-content-center
                            bg-success-subtle rounded-3 mb-3"
                     style="width:52px;height:52px;">
                    <i class="bi bi-cash-stack text-success fs-4"></i>
                </div>
                <!-- number_format affiche le nombre avec des espaces (ex: 1 500) -->
                <h3 class="fw-bold mb-0"><?= number_format($monthly_revenue, 0, ',', ' ') ?></h3>
                <small class="text-muted">Revenus (TND)</small>
            </div>
        </div>
    </div>

    <!-- Carte 6: EmployÃ©s -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card h-100 border-0 text-center">
            <div class="card-body py-4">
                <!-- Violet: Bootstrap 5.3 a bg-info-subtle comme approximation -->
                <div class="d-inline-flex align-items-center justify-content-center
                            bg-info-subtle rounded-3 mb-3"
                     style="width:52px;height:52px;">
                    <i class="bi bi-person-badge-fill text-info fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0"><?= $employes_count ?></h3>
                <small class="text-muted">EmployÃ©s</small>
            </div>
        </div>
    </div>

<!-- ============================================================
     SECTION BASSE: TABLEAU + ACCÃˆS RAPIDE
     ============================================================ -->
<div class="row g-4">

    <!-- ======================================================
         COLONNE GAUCHE: Tableau des derniÃ¨res commandes
         ====================================================== -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-2 text-primary"></i>DerniÃ¨res commandes
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">ID</th>
                            <th class="border-0">Client</th>
                            <th class="border-0">Produit</th>
                            <th class="border-0">Total</th>
                            <th class="border-0">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Boucle sur les commandes rÃ©cupÃ©rÃ©es par la requÃªte JOIN -->
                        <?php foreach ($dernieres_commandes as $cmd): ?>
                        <tr>
                            <td class="text-muted">#<?= $cmd['id'] ?></td>
                            <!-- htmlspecialchars() convertit les caractÃ¨res dangereux en HTML inoffensif -->
                            <td><?= htmlspecialchars($cmd['client_nom'] ?? 'Inconnu') ?></td>
                            <td><?= htmlspecialchars($cmd['produit_nom'] ?? 'Inconnu') ?></td>
                            <td class="fw-semibold"><?= number_format($cmd['total'], 2, ',', ' ') ?> TND</td>
                            <td>
                                <!-- Badge de statut avec une classe CSS dynamique -->
                                <span class="badge badge-<?= $cmd['statut'] ?>">
                                    <?= $cmd['statut'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- Message si aucune commande n'existe -->
                        <?php if (!$dernieres_commandes): ?>
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

    <!-- ======================================================
         COLONNE DROITE: AccÃ¨s rapide + Alertes
         ====================================================== -->
    <div class="col-md-4">

        <!-- AccÃ¨s rapide vers les modules -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-lightning-fill me-2 text-warning"></i>AccÃ¨s rapide
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="../admin/users.php" class="btn btn-outline-primary">
                        <i class="bi bi-people me-2"></i>Utilisateurs
                    </a>
                    <a href="../stock/index.php" class="btn btn-outline-warning">
                        <i class="bi bi-box-seam me-2"></i>Stock
                    </a>
                    <a href="../production/index.php" class="btn btn-outline-info">
                        <i class="bi bi-hammer me-2"></i>Production
                    </a>
                    <a href="../rh/employes.php" class="btn btn-outline-secondary">
                        <i class="bi bi-person-badge me-2"></i>EmployÃ©s
                    </a>
                    <a href="../rh/conges.php" class="btn btn-outline-secondary">
                        <i class="bi bi-calendar-check me-2"></i>CongÃ©s
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerte: CongÃ©s en attente de validation (affichÃ© seulement s'il y en a) -->
        <?php if ($conges_count > 0): ?>
        <div class="card border-0 shadow-sm border-start border-warning border-3">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-exclamation-circle-fill text-warning fs-3 mt-1"></i>
                    <div>
                        <p class="mb-1 fw-semibold">
                            <?= $conges_count ?> congÃ©<?= $conges_count > 1 ? 's' : '' ?> en attente
                        </p>
                        <small class="text-muted">Des employÃ©s attendent votre validation</small>
                    </div>
                </div>
                <a href="../rh/conges.php" class="btn btn-sm btn-warning mt-3 w-100">
                    <i class="bi bi-calendar-check me-1"></i>GÃ©rer les congÃ©s
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Alerte: Stock bas (affichÃ© seulement s'il y a des produits en rupture) -->
        <?php if ($low_stock > 0): ?>
        <div class="card border-0 shadow-sm border-start border-danger border-3 mt-3">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-box-seam text-danger fs-3 mt-1"></i>
                    <div>
                        <p class="mb-1 fw-semibold">
                            <?= $low_stock ?> produit<?= $low_stock > 1 ? 's' : '' ?> en stock bas
                        </p>
                        <small class="text-muted">Le stock doit Ãªtre rÃ©approvisionnÃ©</small>
                    </div>
                </div>
                <a href="../stock/index.php" class="btn btn-sm btn-danger mt-3 w-100">
                    <i class="bi bi-eye me-1"></i>Voir le stock
                </a>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- Inclusion du pied de page commun (footer, scripts JS, etc.) -->
<?php require_once '../includes/footer.php'; ?>
