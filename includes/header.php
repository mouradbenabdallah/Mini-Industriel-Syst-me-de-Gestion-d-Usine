<?php
/**
 * ========================================
 * FICHIER: includes/header.php
 * DESCRIPTION: En-tête commun du projet - Navigation principale
 * PROJET: Usine Industriel - Système de gestion d'usine
 * AUTEUR: Système
 * ========================================
 */

// ==========================================
// CONFIGURATION DE L'URL DE BASE
// ==========================================
// Si la constante BASE_URL n'est pas définie, on la définit dynamiquement
// Cela permet d'avoir des URLs absolues correctes sur tout le site
if (!defined('BASE_URL')) {
    // Détection du protocole (http ou https)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    // Récupération de l'hôte (localhost ou nom de domaine)
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Récupération du chemin du script
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    // Extraction du chemin de base jusqu'au dossier du projet
    $base = preg_replace('#/usine_industriel/.*#', '/usine_industriel/', $script);
    // Définition de la constante BASE_URL
    define('BASE_URL', $protocol . '://' . $host . $base);
}

// ==========================================
// CONNEXION À LA BASE DE DONNÉES
// ==========================================
// Inclusion du fichier de configuration de la base de données
// Cela permet d'avoir accès à l'objet $pdo pour les requêtes
require_once __DIR__ . '/../config/database.php';

// ==========================================
// COMPTEUR DE MESSAGES NON LUS
// ==========================================
// Initialisation du compteur de messages non lus
$unread_count = 0;

// Si un utilisateur est connecté, on récupère le nombre de messages non lus
if (isset($_SESSION['user'])) {
    try {
        // Préparation de la requête SQL pour compter les messages non lus
        $stmt = $pdo->prepare('SELECT id FROM messages WHERE destinataire_id = ? AND lu = 0');
        // Exécution de la requête avec l'ID de l'utilisateur
        $stmt->execute([$_SESSION['user']['id']]);
        // Récupération de tous les messages
        $messages = $stmt->fetchAll();
        // Comptage des messages
        $unread_count = count($messages);
    } catch (PDOException $e) {
        // En cas d'erreur, on ignore silencieusement
        // (le compteur restera à 0)
    }
}

// ==========================================
// MESSAGES FLASH (SUCCÈS/ERREUR)
// ==========================================
// Récupération des messages flash stockés en session
// Ces messages sont affichés une seule fois après une action
$flash_success = $_SESSION['success'] ?? null;
$flash_error   = $_SESSION['error'] ?? null;

// Suppression des messages flash après les avoir récupérés
// pour qu'ils ne s'affichent qu'une seule fois
unset($_SESSION['success'], $_SESSION['error']);

// ==========================================
// VARIABLES DE PAGE
// ==========================================
// Valeurs par défaut pour le titre et la couleur du module
// Ces valeurs peuvent être surchargées dans chaque page
$page_title   = $page_title ?? 'Usine Industriel';
$module_color = $module_color ?? 'primary';

// Récupération du rôle de l'utilisateur connecté
// Utilisé pour afficher/masquer certains éléments selon les droits
$role = $_SESSION['user']['role'] ?? '';

// ==========================================
// PAGE COURANTE
// ==========================================
// Récupération du nom de la page courante
// Utilisé pour mettre en évidence le lien actif dans le menu
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// ==========================================
// CONFIGURATION DES COULEURS
// ==========================================
// Définition des couleurs pour chaque module
// Chaque module a une couleur principale, une couleur claire et une couleur d'accent
$color_config = [
    // Module par défaut - Bleu océan professionnel
    'primary' => ['dark' => '#0c4a6e', 'light' => '#0ea5e9', 'accent' => '#38bdf8'],
    // Module succès - Émeraude vert
    'success' => ['dark' => '#064e3b', 'light' => '#10b981', 'accent' => '#34d399'],
    // Module danger - Rouge vif
    'danger'  => ['dark' => '#7f1d1d', 'light' => '#ef4444', 'accent' => '#f87171'],
    // Module avertissement - Ambre doré
    'warning' => ['dark' => '#78350f', 'light' => '#f59e0b', 'accent' => '#fbbf24'],
    // Module info - Cyan éclatant
    'info'    => ['dark' => '#164e63', 'light' => '#06b6d4', 'accent' => '#22d3ee'],
    // Module sombre - Bleu nuit élégant
    'dark'    => ['dark' => '#1e3a8a', 'light' => '#3b82f6', 'accent' => '#93c5fd'],
    // Module RH - Violet royal
    'rh'      => ['dark' => '#581c87', 'light' => '#8b5cf6', 'accent' => '#a78bfa'],
    // Module teal - Turquoise tropical
    'teal'    => ['dark' => '#134e4a', 'light' => '#14b8a6', 'accent' => '#2dd4bf']
];

// Récupération des couleurs pour le module courant
// Utilisation de la couleur primary par défaut
$colors = $color_config[$module_color] ?? $color_config['primary'];

// ==========================================
// REDIRECTIONS PAR DÉFAUT
// ==========================================
// Tableau de correspondance rôle -> URL du dashboard
// Permet d'afficher le bon tableau de bord selon le rôle
$dash_url = [
    'admin'   => 'dashboard/admin_dashboard.php',
    'manager' => 'dashboard/manager_dashboard.php',
    'employe' => 'dashboard/employe_dashboard.php',
    'client'  => 'dashboard/client_dashboard.php'
];

// URL de la page des messages selon le rôle
$msg_pages = [
    'admin'   => 'admin',
    'manager' => 'production',
    'employe' => 'production',
    'client'  => 'ventes'
];

// Récupération de l'URL du dashboard de l'utilisateur
$dashboard_url = $dash_url[$role] ?? 'dashboard/admin_dashboard.php';
// Récupération de l'URL de la page des messages
$msg_url = BASE_URL . ($msg_pages[$role] ?? 'admin') . '/messages.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Méta-données -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | Usine Industriel</title>
    <meta name="description" content="Système de gestion d'usine industrielle - Production, Stock, RH, Ventes">

    <!-- CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icônes Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Google Fonts - Police moderne et professionnelle -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Feuille de style personnalisée -->
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">

    <!-- Favicon (icône dans l'onglet du navigateur) -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏭</text></svg>">

    <!-- Variables CSS personnalisées pour les couleurs du module -->
    <style>
    :root {
        /* Couleurs du module courant */
        --primary-dark: <?=$colors['dark'] ?>;
        --primary-light: <?=$colors['light'] ?>;
        --accent-color: <?=$colors['accent'] ?>;
    }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- ==========================================
         TOP BAR - Barre d'information
         ========================================== -->
    <?php if (isset($_SESSION['user'])): ?>
    <div class="top-notification-bar"
        style="background: linear-gradient(90deg, var(--primary-dark) 0%, var(--primary-light) 100%);">
        <div class="container-fluid px-4">
            <div class="row align-items-center py-2">
                <!-- Partie gauche: Bienvenue -->
                <div class="col-md-6 d-none d-md-block">
                    <div class="d-flex align-items-center gap-2 text-white-50 small">
                        <span>
                            <i class="bi bi-person-circle me-1"></i>
                            Bienvenue, <?= htmlspecialchars($_SESSION['user']['nom'] ?? 'Utilisateur') ?>
                        </span>
                    </div>
                </div>

                <!-- Partie droite: Messages -->
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end align-items-center gap-3">
                        <a href="<?= $msg_url ?>" class="top-link position-relative">
                            <i class="bi bi-bell me-1"></i>Messages
                            <?php if ($unread_count > 0): ?>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $unread_count ?>
                            </span>
                            <?php endif; ?>
                        </a>
                        <!-- Profil utilisateur -->
                        <?php
                        $user = $_SESSION['user'];
                        $user_initials = strtoupper(substr($user['nom'] ?? 'U', 0, 2));
                        ?>
                        <div class="dropdown">
                            <a class="profile-trigger text-white text-decoration-none d-flex align-items-center gap-2"
                                href="#" data-bs-toggle="dropdown">
                                <div class="profile-avatar-sm bg-white text-dark fw-bold">
                                    <?= $user_initials ?>
                                </div>
                                <span
                                    class="d-none d-md-inline"><?= htmlspecialchars($user['nom'] ?? 'Utilisateur') ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-profile shadow-lg border-0 rounded-3 p-2">
                                <li><span class="dropdown-item-text text-muted small">
                                        <i
                                            class="bi bi-person-circle me-1"></i><?= htmlspecialchars($user['email'] ?? '') ?>
                                    </span></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>auth/logout.php">
                                        <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ==========================================
         NAVBAR PRINCIPALE
         ========================================== -->
    <nav class="main-navbar"
        style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <div class="container-fluid px-4">
            <div class="row align-items-center">

                <!-- LOGO -->
                <div class="col-auto">
                    <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>index.php">
                        <div class="logo-wrapper">
                            <div class="logo-icon">
                                <i class="bi bi-gear-fill"></i>
                            </div>
                            <div class="logo-text">
                                <span class="brand-main">Usine</span>
                                <span class="brand-sub">Industriel</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- BOUTON NOUVEAU (Quick Add) - Visible sur desktop -->
                <?php if (isset($_SESSION['user'])): ?>
                <div class="col-auto d-none d-md-block">
                    <div class="dropdown">
                        <button class="btn btn-quick-add"
                            style="background: var(--accent-color); color: var(--primary-dark);" type="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-plus-lg me-1"></i>
                            <span class="d-none d-lg-inline">Nouveau</span>
                        </button>
                        <!-- Menu déroulant pour créer de nouveaux éléments -->
                        <ul class="dropdown-menu dropdown-menu-light shadow-lg border-0 rounded-3 p-2"
                            style="min-width: 220px;">
                            <?php if (in_array($role, ['admin','manager','employe'])): ?>
                            <li>
                                <h6 class="dropdown-header text-muted small">Production</h6>
                            </li>
                            <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>production/create.php">
                                    <i class="bi bi-hammer text-primary me-2"></i>Nouvelle Production
                                </a></li>
                            <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>stock/create.php">
                                    <i class="bi bi-box-seam text-success me-2"></i>Nouveau Produit
                                </a></li>
                            <?php endif; ?>

                            <?php if (in_array($role, ['admin','manager','client'])): ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <h6 class="dropdown-header text-muted small">Ventes</h6>
                            </li>
                            <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>ventes/commande_add.php">
                                    <i class="bi bi-cart text-warning me-2"></i>Nouvelle Commande
                                </a></li>
                            <?php endif; ?>

                            <?php if ($role === 'admin'): ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <h6 class="dropdown-header text-muted small">Administration</h6>
                            </li>
                            <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>admin/user_add.php">
                                    <i class="bi bi-person-plus text-info me-2"></i>Nouvel Utilisateur
                                </a></li>
                            <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>rh/employe_add.php">
                                    <i class="bi bi-person-badge text-purple me-2"></i>Nouvel Employé
                                </a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

                <!-- MENU DE NAVIGATION - Centre -->
                <div class="col d-none d-lg-block">
                    <?php if (isset($_SESSION['user'])): ?>
                    <ul class="nav nav-main mx-auto justify-content-center">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link <?= in_array($current_page, ['admin_dashboard','manager_dashboard','employe_dashboard','client_dashboard','index']) ? 'active' : '' ?>"
                                href="<?= BASE_URL . $dashboard_url ?>">
                                <i class="bi bi-grid-1x2 me-1"></i>Dashboard
                            </a>
                        </li>

                        <!-- Production - Visible pour admin, manager, employé -->
                        <?php if (in_array($role, ['admin','manager','employe'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($current_page, 'production') !== false ? 'active' : '' ?>"
                                href="<?= BASE_URL ?>production/index.php">
                                <i class="bi bi-hammer me-1"></i>Production
                            </a>
                        </li>

                        <!-- Stock - Visible pour admin, manager, employé -->
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($current_page, 'stock') !== false ? 'active' : '' ?>"
                                href="<?= BASE_URL ?>stock/index.php">
                                <i class="bi bi-boxes me-1"></i>Stock
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- RH - Visible pour admin et manager uniquement -->
                        <?php if (in_array($role, ['admin','manager'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($current_page, 'rh') !== false ? 'active' : '' ?>"
                                href="<?= BASE_URL ?>rh/employes.php">
                                <i class="bi bi-people me-1"></i>RH
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Ventes - Visible pour admin, manager, client -->
                        <?php if (in_array($role, ['admin','manager','client'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($current_page, 'vente') !== false ? 'active' : '' ?>"
                                href="<?= BASE_URL ?>ventes/commandes.php">
                                <i class="bi bi-cart-check me-1"></i>Ventes
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Admin - Visible uniquement pour admin -->
                        <?php if ($role === 'admin'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-shield-lock me-1"></i>Admin
                            </a>
                            <ul class="dropdown-menu dropdown-menu-light shadow-lg border-0 rounded-3 p-2">
                                <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>admin/users.php">
                                        <i class="bi bi-people me-2"></i>Utilisateurs
                                    </a></li>
                                <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>admin/statistics.php">
                                        <i class="bi bi-bar-chart-line me-2"></i>Statistiques
                                    </a></li>
                                <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>admin/messages.php">
                                        <i class="bi bi-envelope me-2"></i>Messages
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>admin/user_add.php">
                                        <i class="bi bi-person-plus me-2"></i>Ajouter utilisateur
                                    </a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>
                </div>

                <!-- ACTIONS DROITES - Icônes et profil -->
                <div class="col-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                    <div class="d-flex align-items-center gap-2">

                        <!-- Recherche - Visible sur grand écran -->
                        <div class="search-wrapper d-none d-xl-block">
                            <form action="#" class="d-flex">
                                <div class="input-group input-search">
                                    <input type="text" class="form-control form-control-sm" placeholder="Rechercher...">
                                    <button class="btn btn-sm" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Messages -->
                        <a href="<?= $msg_url ?>" class="btn btn-icon" title="Messages">
                            <i class="bi bi-envelope"></i>
                            <?php if ($unread_count > 0): ?>
                            <span class="badge-dot" style="background: #e74c3c;"></span>
                            <?php endif; ?>
                        </a>

                        <!-- Notifications -->
                        <div class="dropdown">
                            <button class="btn btn-icon position-relative" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <span class="badge-dot" style="background: var(--accent-color);"></span>
                            </button>
                            <!-- Menu des notifications -->
                            <div class="dropdown-menu dropdown-menu-end dropdown-notif shadow-lg border-0 rounded-3 p-0"
                                style="width: 340px;">
                                <div class="notif-header"
                                    style="background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));">
                                    <div class="d-flex justify-content-between align-items-center p-3">
                                        <h6 class="mb-0 text-white">
                                            <i class="bi bi-bell me-2"></i>Notifications
                                        </h6>
                                        <span class="badge bg-white text-dark rounded-pill">
                                            <?= $unread_count > 0 ? $unread_count : '0' ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="notif-body" style="max-height: 280px; overflow-y: auto;">
                                    <a href="#" class="notif-item">
                                        <div class="notif-icon"
                                            style="background: linear-gradient(135deg, var(--accent-color), var(--primary-light));">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <div class="notif-content">
                                            <p class="mb-0">Bienvenue sur Usine Industriel</p>
                                            <small class="text-muted">À l'instant</small>
                                        </div>
                                    </a>
                                    <a href="<?= $msg_url ?>" class="notif-item">
                                        <div class="notif-icon bg-success">
                                            <i class="bi bi-envelope"></i>
                                        </div>
                                        <div class="notif-content">
                                            <p class="mb-0">Vous avez <?= $unread_count ?> message(s) non lu(s)</p>
                                            <small class="text-muted">Cliquer pour voir</small>
                                        </div>
                                    </a>
                                </div>
                                <div class="notif-footer p-2 text-center border-top">
                                    <a href="#" class="small">Voir toutes les notifications</a>
                                </div>
                            </div>
                        </div>

                        <!-- Profil utilisateur -->
                        <div class="dropdown">
                            <a href="#" class="profile-trigger d-flex align-items-center gap-2"
                                data-bs-toggle="dropdown">
                                <div class="profile-avatar-sm"
                                    style="background: linear-gradient(135deg, var(--accent-color), var(--primary-light));">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div class="d-none d-xl-block text-start">
                                    <div class="small text-white fw-semibold">
                                        <?= htmlspecialchars($_SESSION['user']['nom']) ?>
                                    </div>
                                    <div class="xsmall text-white-50" style="font-size: 0.7rem;">
                                        <?= ucfirst($role) // Première lettre en majuscule ?>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-down text-white-50 small"></i>
                            </a>
                            <!-- Menu du profil -->
                            <div class="dropdown-menu dropdown-menu-end dropdown-profile shadow-lg border-0 rounded-3 p-0"
                                style="width: 260px;">
                                <div class="profile-card-header"
                                    style="background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));">
                                    <div class="text-center py-3">
                                        <div class="profile-avatar-lg mx-auto mb-2"
                                            style="background: linear-gradient(135deg, var(--accent-color), var(--primary-light));">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <h6 class="text-white mb-0">
                                            <?= htmlspecialchars($_SESSION['user']['nom']) ?>
                                        </h6>
                                        <small class="text-white-50">
                                            <?= htmlspecialchars($_SESSION['user']['email'] ?? 'email@exemple.com') ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <a href="#" class="dropdown-item rounded-2">
                                        <i class="bi bi-person me-2"></i>Mon Profil
                                    </a>
                                    <a href="#" class="dropdown-item rounded-2">
                                        <i class="bi bi-gear me-2"></i>Paramètres
                                    </a>
                                    <a href="#" class="dropdown-item rounded-2">
                                        <i class="bi bi-palette me-2"></i>Apparence
                                    </a>
                                    <hr class="my-2">
                                    <a href="<?= BASE_URL ?>auth/logout.php"
                                        class="dropdown-item rounded-2 text-danger">
                                        <i class="bi bi-box-arrow-left me-2"></i>Déconnexion
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- BOUTON HAMBURGER - Mobile uniquement -->
                <div class="col-auto d-lg-none">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- ==========================================
         NAVIGATION MOBILE
         ========================================== -->
    <div class="collapse d-lg-none" id="mobileNav">
        <div class="bg-dark p-3">
            <?php if (isset($_SESSION['user'])): ?>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= BASE_URL . $dashboard_url ?>">
                        <i class="bi bi-grid-1x2 me-2"></i>Dashboard
                    </a>
                </li>
                <?php if (in_array($role, ['admin','manager','employe'])): ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= BASE_URL ?>production/index.php">
                        <i class="bi bi-hammer me-2"></i>Production
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= BASE_URL ?>stock/index.php">
                        <i class="bi bi-boxes me-2"></i>Stock
                    </a>
                </li>
                <?php endif; ?>
                <?php if (in_array($role, ['admin','manager'])): ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= BASE_URL ?>rh/employes.php">
                        <i class="bi bi-people me-2"></i>RH
                    </a>
                </li>
                <?php endif; ?>
                <?php if (in_array($role, ['admin','manager','client'])): ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= BASE_URL ?>ventes/commandes.php">
                        <i class="bi bi-cart-check me-2"></i>Ventes
                    </a>
                </li>
                <?php endif; ?>
                <?php if ($role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= BASE_URL ?>admin/users.php">
                        <i class="bi bi-shield-lock me-2"></i>Admin
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <hr class="text-white">
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="<?= BASE_URL ?>auth/logout.php">
                        <i class="bi bi-box-arrow-left me-2"></i>Déconnexion
                    </a>
                </li>
            </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- ==========================================
         FIL D'ARIANE (Breadcrumb)
         ========================================== -->
    <?php if (isset($_SESSION['user']) && $page_title !== 'Dashboard'): ?>
    <nav class="breadcrumb-nav bg-white border-bottom">
        <div class="container-fluid px-4 py-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="<?= BASE_URL ?>index.php" class="text-decoration-none">
                        <i class="bi bi-house"></i>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <?= htmlspecialchars($page_title) ?>
                </li>
            </ol>
        </div>
    </nav>
    <?php endif; ?>

    <!-- ==========================================
         CONTENU PRINCIPAL
         ========================================== -->
    <main class="flex-grow-1 main-content">
        <div class="container-fluid px-4 py-4">

            <!-- Message de succès (vert) -->
            <?php if ($flash_success): ?>
            <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($flash_success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Message d'erreur (rouge) -->
            <?php if ($flash_error): ?>
            <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($flash_error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>