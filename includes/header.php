<?php
/**
 * header.php — Navigation principale
 * Projet: Usine Industriel
 */

// ── BASE URL ──────────────────────────────────────────────────────────────────
if (!defined('BASE_URL')) {
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host  = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base  = preg_replace('#/usine_industriel/.*#', '/usine_industriel/', $_SERVER['SCRIPT_NAME'] ?? '');
    define('BASE_URL', $proto . '://' . $host . $base);
}

// ── BASE DE DONNÉES ───────────────────────────────────────────────────────────
require_once __DIR__ . '/../config/database.php';

// ── SESSION / UTILISATEUR ─────────────────────────────────────────────────────
$user          = $_SESSION['user'] ?? null;
$role          = $user['role'] ?? '';
$user_initials = $user ? strtoupper(substr($user['nom'] ?? 'U', 0, 2)) : '';

// ── MESSAGES NON LUS ──────────────────────────────────────────────────────────
$unread_count = 0;
if ($user) {
    try {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM messages WHERE destinataire_id = ? AND lu = 0');
        $stmt->execute([$user['id']]);
        $unread_count = (int) $stmt->fetchColumn();
    } catch (PDOException $e) { /* silence */ }
}

// ── MESSAGES FLASH ────────────────────────────────────────────────────────────
$flash_success = $_SESSION['success'] ?? null;
$flash_error   = $_SESSION['error']   ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// ── PAGE ──────────────────────────────────────────────────────────────────────
$page_title   = $page_title   ?? 'Usine Industriel';
$module_color = $module_color ?? 'primary';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// ── COULEURS PAR MODULE ───────────────────────────────────────────────────────
$colors_map = [
    'primary' => ['dark' => '#0c4a6e', 'mid' => '#0369a1', 'light' => '#38bdf8'],
    'success' => ['dark' => '#064e3b', 'mid' => '#059669', 'light' => '#34d399'],
    'danger'  => ['dark' => '#7f1d1d', 'mid' => '#dc2626', 'light' => '#f87171'],
    'warning' => ['dark' => '#78350f', 'mid' => '#d97706', 'light' => '#fbbf24'],
    'rh'      => ['dark' => '#581c87', 'mid' => '#7c3aed', 'light' => '#a78bfa'],
    'teal'    => ['dark' => '#134e4a', 'mid' => '#0d9488', 'light' => '#2dd4bf'],
];
$c = $colors_map[$module_color] ?? $colors_map['primary'];

// ── DASHBOARDS & MESSAGES PAR RÔLE ───────────────────────────────────────────
$dashboard_url = match($role) {
    'manager' => 'dashboard/manager_dashboard.php',
    'employe' => 'dashboard/employe_dashboard.php',
    'client'  => 'dashboard/client_dashboard.php',
    default   => 'dashboard/admin_dashboard.php',
};
$msg_section = match($role) {
    'manager', 'employe' => 'production',
    'client'             => 'ventes',
    default              => 'admin',
};
$msg_url = BASE_URL . $msg_section . '/messages.php';

// ── HELPERS ───────────────────────────────────────────────────────────────────
function nav_active(string $current, array $pages): string {
    return in_array($current, $pages) ? 'active' : '';
}
function nav_contains(string $current, string $keyword): string {
    return str_contains($current, $keyword) ? 'active' : '';
}
function can(string $role, array $allowed): bool {
    return in_array($role, $allowed);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | Usine Industriel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">

    <!-- Module color variables only -->
    <style>
    :root {
        --c-dark: <?=$c['dark'] ?>;
        --c-mid: <?=$c['mid'] ?>;
        --c-light: <?=$c['light'] ?>;
    }
    </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <?php if ($user): ?>
    <!-- ── TOP BAR ─────────────────────────────────────────────────────────────── -->
    <div class="d-flex align-items-center gap-3 px-4 py-1 small" style="background:var(--c-dark); min-height:34px;">
        <span class="text-white-50 me-auto d-none d-md-inline">
            Bienvenue, <strong class="text-white"><?= htmlspecialchars($user['nom'] ?? 'Utilisateur') ?></strong>
        </span>

        <a href="<?= $msg_url ?>" class="text-white-50 text-decoration-none d-flex align-items-center gap-1">
            <i class="bi bi-envelope"></i> Messages
            <?php if ($unread_count > 0): ?>
            <span class="badge rounded-pill bg-danger"><?= $unread_count ?></span>
            <?php endif; ?>
        </a>

        <span class="text-white-50">|</span>

        <div class="dropdown">
            <a href="#" class="text-white-50 text-decoration-none d-flex align-items-center gap-1"
                data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
                <?= htmlspecialchars($user['nom'] ?? '') ?>
                <i class="bi bi-chevron-down" style="font-size:.6rem;"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-2 mt-1" style="min-width:180px;">
                <li><span
                        class="dropdown-item-text text-muted small"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                </li>
                <li>
                    <hr class="dropdown-divider my-1">
                </li>
                <li>
                    <a class="dropdown-item rounded-2 text-danger small" href="<?= BASE_URL ?>auth/logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── MAIN NAVBAR ─────────────────────────────────────────────────────────── -->
    <nav class="navbar navbar-expand-lg sticky-top shadow-sm px-4"
        style="background:linear-gradient(135deg, var(--c-dark) 0%, var(--c-mid) 100%); min-height:58px;">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="<?= BASE_URL ?>index.php">
            <div class="d-flex align-items-center justify-content-center rounded-2 border border-white border-opacity-25"
                style="width:34px;height:34px;background:rgba(255,255,255,.1);">
                <i class="bi bi-gear-fill" style="color:var(--c-light);"></i>
            </div>
            <div class="lh-1">
                <div class="fw-bold text-white" style="font-size:.92rem;">Usine</div>
                <div class="text-white-50" style="font-size:.6rem;letter-spacing:.08em;text-transform:uppercase;">
                    Industriel</div>
            </div>
        </a>

        <?php if ($user): ?>

        <!-- QUICK ADD -->
        <div class="dropdown d-none d-lg-block ms-3">
            <button class="btn btn-sm fw-semibold border-0 d-flex align-items-center gap-1"
                style="background:var(--c-light);color:var(--c-dark);" data-bs-toggle="dropdown">
                <i class="bi bi-plus-lg"></i> Nouveau
            </button>
            <ul class="dropdown-menu shadow border-0 rounded-3 p-2 mt-2" style="min-width:210px;">
                <?php if (can($role, ['admin','manager','employe'])): ?>
                <li>
                    <h6 class="dropdown-header text-muted small">Production</h6>
                </li>
                <li><a class="dropdown-item rounded-2 small" href="<?= BASE_URL ?>production/create.php"><i
                            class="bi bi-hammer text-primary me-2"></i>Nouvelle Production</a></li>
                <li><a class="dropdown-item rounded-2 small" href="<?= BASE_URL ?>stock/create.php"><i
                            class="bi bi-box-seam text-success me-2"></i>Nouveau Produit</a></li>
                <?php endif; ?>
                <?php if (can($role, ['admin','manager','client'])): ?>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <h6 class="dropdown-header text-muted small">Ventes</h6>
                </li>
                <li><a class="dropdown-item rounded-2 small" href="<?= BASE_URL ?>ventes/commande_add.php"><i
                            class="bi bi-cart text-warning me-2"></i>Nouvelle Commande</a></li>
                <?php endif; ?>
                <?php if ($role === 'admin'): ?>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <h6 class="dropdown-header text-muted small">Administration</h6>
                </li>
                <li><a class="dropdown-item rounded-2 small" href="<?= BASE_URL ?>admin/user_add.php"><i
                            class="bi bi-person-plus text-info me-2"></i>Nouvel Utilisateur</a></li>
                <li><a class="dropdown-item rounded-2 small" href="<?= BASE_URL ?>rh/employe_add.php"><i
                            class="bi bi-person-badge text-purple me-2"></i>Nouvel Employé</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- HAMBURGER -->
        <button class="navbar-toggler border-0 ms-auto me-2 d-lg-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#mobileNav">
            <i class="bi bi-list text-white fs-4"></i>
        </button>

        <!-- NAV LINKS -->
        <div class="collapse navbar-collapse justify-content-center" id="desktopNav">
            <ul class="navbar-nav gap-1">
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-2 small fw-medium
                    <?= nav_active($current_page, ['admin_dashboard','manager_dashboard','employe_dashboard','client_dashboard','index']) ?>"
                        href="<?= BASE_URL . $dashboard_url ?>" style="color:rgba(255,255,255,.7);">
                        <i class="bi bi-grid-1x2 me-1"></i>Dashboard
                    </a>
                </li>

                <?php if (can($role, ['admin','manager','employe'])): ?>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-2 small fw-medium <?= nav_contains($current_page, 'production') ?>"
                        href="<?= BASE_URL ?>production/index.php" style="color:rgba(255,255,255,.7);">
                        <i class="bi bi-hammer me-1"></i>Production
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-2 small fw-medium <?= nav_contains($current_page, 'stock') ?>"
                        href="<?= BASE_URL ?>stock/index.php" style="color:rgba(255,255,255,.7);">
                        <i class="bi bi-boxes me-1"></i>Stock
                    </a>
                </li>
                <?php endif; ?>

                <?php if (can($role, ['admin','manager'])): ?>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-2 small fw-medium <?= nav_contains($current_page, 'rh') ?>"
                        href="<?= BASE_URL ?>rh/employes.php" style="color:rgba(255,255,255,.7);">
                        <i class="bi bi-people me-1"></i>RH
                    </a>
                </li>
                <?php endif; ?>

                <?php if (can($role, ['admin','manager','client'])): ?>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-2 small fw-medium <?= nav_contains($current_page, 'vente') ?>"
                        href="<?= BASE_URL ?>ventes/commandes.php" style="color:rgba(255,255,255,.7);">
                        <i class="bi bi-cart-check me-1"></i>Ventes
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($role === 'admin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link px-3 py-2 rounded-2 small fw-medium dropdown-toggle" href="#"
                        data-bs-toggle="dropdown" style="color:rgba(255,255,255,.7);">
                        <i class="bi bi-shield-lock me-1"></i>Admin
                    </a>
                    <ul class="dropdown-menu shadow border-0 rounded-3 p-2 mt-2">
                        <li><a class="dropdown-item rounded-2 small" href="<?= BASE_URL ?>admin/users.php"><i
                                    class="bi bi-people text-primary me-2"></i>Utilisateurs</a></li>
                        <li><a class="dropdown-item rounded-2 small" href="<?= BASE_URL ?>admin/statistics.php"><i
                                    class="bi bi-bar-chart-line text-success me-2"></i>Statistiques</a></li>
                        <li><a class="dropdown-item rounded-2 small" href="<?= BASE_URL ?>admin/messages.php"><i
                                    class="bi bi-envelope text-info me-2"></i>Messages</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item rounded-2 small" href="<?= BASE_URL ?>admin/user_add.php"><i
                                    class="bi bi-person-plus text-warning me-2"></i>Ajouter utilisateur</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- RIGHT ICONS -->
        <div class="d-none d-lg-flex align-items-center gap-2 ms-3">

            <!-- Messages -->
            <a href="<?= $msg_url ?>"
                class="btn btn-sm border-0 rounded-2 position-relative d-flex align-items-center justify-content-center"
                style="width:36px;height:36px;background:rgba(255,255,255,.12);color:rgba(255,255,255,.8);"
                title="Messages">
                <i class="bi bi-envelope"></i>
                <?php if ($unread_count > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    style="font-size:.55rem;"><?= $unread_count ?></span>
                <?php endif; ?>
            </a>

            <!-- Notifications -->
            <div class="dropdown">
                <button
                    class="btn btn-sm border-0 rounded-2 position-relative d-flex align-items-center justify-content-center"
                    style="width:36px;height:36px;background:rgba(255,255,255,.12);color:rgba(255,255,255,.8);"
                    data-bs-toggle="dropdown" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 rounded-circle"
                        style="background:var(--c-light);width:8px;height:8px;"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-0 overflow-hidden mt-2"
                    style="width:300px;">
                    <div class="d-flex justify-content-between align-items-center p-3"
                        style="background:linear-gradient(135deg, var(--c-dark), var(--c-mid));">
                        <h6 class="mb-0 text-white small"><i class="bi bi-bell me-2"></i>Notifications</h6>
                        <span class="badge rounded-2 small"
                            style="background:rgba(255,255,255,.15);color:var(--c-light);"><?= $unread_count ?></span>
                    </div>
                    <a href="#" class="d-flex align-items-center gap-3 p-3 text-decoration-none border-bottom">
                        <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                            style="width:32px;height:32px;background:#dbeafe;color:#2563eb;">
                            <i class="bi bi-check-lg small"></i>
                        </div>
                        <div>
                            <p class="mb-0 small text-dark">Bienvenue sur Usine Industriel</p>
                            <small class="text-muted">À l'instant</small>
                        </div>
                    </a>
                    <?php if ($unread_count > 0): ?>
                    <a href="<?= $msg_url ?>"
                        class="d-flex align-items-center gap-3 p-3 text-decoration-none border-bottom">
                        <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                            style="width:32px;height:32px;background:#dcfce7;color:#16a34a;">
                            <i class="bi bi-envelope small"></i>
                        </div>
                        <div>
                            <p class="mb-0 small text-dark"><?= $unread_count ?> message(s) non lu(s)</p>
                            <small class="text-muted">Cliquer pour voir</small>
                        </div>
                    </a>
                    <?php endif; ?>
                    <div class="p-2 text-center">
                        <a href="#" class="text-decoration-none small" style="color:var(--c-mid);">Toutes les
                            notifications</a>
                    </div>
                </div>
            </div>

            <!-- Profile -->
            <div class="dropdown">
                <div class="d-flex align-items-center justify-content-center rounded-2 fw-bold text-white border border-white border-opacity-25"
                    style="width:34px;height:34px;background:linear-gradient(135deg,var(--c-light),var(--c-mid));font-size:.75rem;cursor:pointer;"
                    data-bs-toggle="dropdown" title="Profil">
                    <?= $user_initials ?>
                </div>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-0 overflow-hidden mt-2"
                    style="min-width:220px;">
                    <div class="d-flex align-items-center gap-3 p-3"
                        style="background:linear-gradient(135deg, var(--c-dark), var(--c-mid));">
                        <div class="d-flex align-items-center justify-content-center rounded-2 fw-bold text-white border border-white border-opacity-25 flex-shrink-0"
                            style="width:40px;height:40px;background:rgba(255,255,255,.15);font-size:.85rem;">
                            <?= $user_initials ?>
                        </div>
                        <div>
                            <div class="fw-semibold text-white small"><?= htmlspecialchars($user['nom'] ?? '') ?></div>
                            <div class="text-white-50" style="font-size:.7rem;">
                                <?= htmlspecialchars($user['email'] ?? '') ?></div>
                            <span class="badge rounded-1 mt-1"
                                style="font-size:.6rem;background:rgba(255,255,255,.12);color:var(--c-light);">
                                <?= ucfirst($role) ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-2">
                        <a href="#" class="dropdown-item rounded-2 small"><i class="bi bi-person me-2"></i>Mon
                            Profil</a>
                        <a href="#" class="dropdown-item rounded-2 small"><i class="bi bi-gear me-2"></i>Paramètres</a>
                        <hr class="dropdown-divider">
                        <a href="<?= BASE_URL ?>auth/logout.php" class="dropdown-item rounded-2 small text-danger">
                            <i class="bi bi-box-arrow-left me-2"></i>Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </nav>

    <!-- ── MOBILE NAV ──────────────────────────────────────────────────────────── -->
    <?php if ($user): ?>
    <div class="collapse d-lg-none" id="mobileNav" style="background:#1e293b;">
        <div class="px-3 py-2">
            <a class="d-flex align-items-center gap-2 py-2 text-white-50 text-decoration-none small"
                href="<?= BASE_URL . $dashboard_url ?>">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
            <?php if (can($role, ['admin','manager','employe'])): ?>
            <a class="d-flex align-items-center gap-2 py-2 text-white-50 text-decoration-none small"
                href="<?= BASE_URL ?>production/index.php"><i class="bi bi-hammer"></i> Production</a>
            <a class="d-flex align-items-center gap-2 py-2 text-white-50 text-decoration-none small"
                href="<?= BASE_URL ?>stock/index.php"><i class="bi bi-boxes"></i> Stock</a>
            <?php endif; ?>
            <?php if (can($role, ['admin','manager'])): ?>
            <a class="d-flex align-items-center gap-2 py-2 text-white-50 text-decoration-none small"
                href="<?= BASE_URL ?>rh/employes.php"><i class="bi bi-people"></i> RH</a>
            <?php endif; ?>
            <?php if (can($role, ['admin','manager','client'])): ?>
            <a class="d-flex align-items-center gap-2 py-2 text-white-50 text-decoration-none small"
                href="<?= BASE_URL ?>ventes/commandes.php"><i class="bi bi-cart-check"></i> Ventes</a>
            <?php endif; ?>
            <?php if ($role === 'admin'): ?>
            <a class="d-flex align-items-center gap-2 py-2 text-white-50 text-decoration-none small"
                href="<?= BASE_URL ?>admin/users.php"><i class="bi bi-shield-lock"></i> Admin</a>
            <?php endif; ?>
            <hr class="border-white border-opacity-10">
            <a class="d-flex align-items-center gap-2 py-2 text-danger text-decoration-none small"
                href="<?= BASE_URL ?>auth/logout.php"><i class="bi bi-box-arrow-left"></i> Déconnexion</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── BREADCRUMB ──────────────────────────────────────────────────────────── -->
    <?php if ($user && $page_title !== 'Dashboard'): ?>
    <nav class="bg-white border-bottom px-4 py-2">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item">
                <a href="<?= BASE_URL ?>index.php" class="text-decoration-none" style="color:var(--c-mid);">
                    <i class="bi bi-house-fill"></i>
                </a>
            </li>
            <li class="breadcrumb-item active text-secondary"><?= htmlspecialchars($page_title) ?></li>
        </ol>
    </nav>
    <?php endif; ?>

    <!-- ── MAIN CONTENT ────────────────────────────────────────────────────────── -->
    <main class="flex-grow-1 p-4">

        <?php if ($flash_success): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 rounded-3 border-0 small"
            role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <?= htmlspecialchars($flash_success) ?>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($flash_error): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 rounded-3 border-0 small"
            role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= htmlspecialchars($flash_error) ?>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>