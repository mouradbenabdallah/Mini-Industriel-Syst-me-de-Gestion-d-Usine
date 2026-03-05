<?php
/**
 * En-tête commun du projet
 * Ce fichier est inclus dans toutes les pages pour afficher la navigation et les messages flash
 * 
 * Fonctionnalités:
 * - Définition de l'URL de base du site
 * - Comptage des messages non lus
 * - Affichage des messages de succès/erreur (flash messages)
 * - Navigation principale selon le rôle de l'utilisateur
 * 
 * Projet: Usine Industriel - Système de gestion d'usine
 * Mode: Procédural (sans POO)
 */

// Si la constante BASE_URL n'est pas définie, la définir dynamiquement
if (!defined('BASE_URL')) {
    // Détermination du protocole (http ou https)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    // Extraction du chemin de base jusqu'au dossier du projet
    $base = preg_replace('#/usine_industriel/.*#', '/usine_industriel/', $script);
    define('BASE_URL', $protocol . '://' . $host . $base);
}

// Inclusion de la configuration de la base de données pour avoir accès à $pdo
// Cela permet de compter les messages non lus
require_once __DIR__ . '/../config/database.php';

// Initialisation du compteur de messages non lus
$unread_count = 0;

// Si un utilisateur est connecté, récupérer le nombre de messages non lus
if (isset($_SESSION['user'])) {
    try {
        // Récupérer tous les messages non lus et les compter en PHP
        $stmt = $pdo->prepare('SELECT id FROM messages WHERE destinataire_id = ? AND lu = 0');
        $stmt->execute([$_SESSION['user']['id']]);
        $messages = $stmt->fetchAll();
        $unread_count = count($messages);
    } catch (PDOException $e) {
        // En cas d'erreur, on ignore silencieusement
    }
}

// Récupération des messages flash (succès ou erreur) stockés en session
$flash_success = $_SESSION['success'] ?? null;
$flash_error   = $_SESSION['error'] ?? null;
// Suppression des messages flash après les avoir récupérés
unset($_SESSION['success'], $_SESSION['error']);

// Valeurs par défaut pour le titre et la couleur du module
$page_title   = $page_title ?? 'Usine Industriel';
$module_color = $module_color ?? 'primary';

// Récupération du rôle de l'utilisateur connecté
$role = $_SESSION['user']['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> Usine Industriel</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Feuille de style personnalisée -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>

<body>

    <!-- Barre de navigation principale -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-<?= htmlspecialchars($module_color) ?> mb-4 shadow-sm">
        <div class="container-fluid px-4">
            <!-- Logo et titre du site -->
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= BASE_URL ?>index.php">
                <i class="bi bi-gear-fill me-2"></i> Usine Industriel
            </a>
            <!-- Bouton hamburger pour mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu de navigation -->
            <div class="collapse navbar-collapse" id="navMain">
                <?php if (isset($_SESSION['user'])): ?>
                <ul class="navbar-nav me-auto">
                    <!-- Lien Production (pour admin, manager, employé) -->
                    <?php if (in_array($role, ['admin','manager','employe'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>production/index.php">
                            <i class="bi bi-wrench me-1"></i> Production
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Lien Stock (pour admin, manager, employé) -->
                    <?php if (in_array($role, ['admin','manager','employe'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>stock/index.php">
                            <i class="bi bi-box-seam me-1"></i> Stock
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Lien RH (pour admin et manager) -->
                    <?php if (in_array($role, ['admin','manager'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>rh/employes.php">
                            <i class="bi bi-people me-1"></i> RH
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Lien Ventes (pour admin, manager, client) -->
                    <?php if (in_array($role, ['admin','manager','client'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>ventes/commandes.php">
                            <i class="bi bi-cart me-1"></i> Ventes
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Menu Admin (pour admin seulement) -->
                    <?php if ($role === 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-shield-lock me-1"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/users.php"><i
                                        class="bi bi-people me-2"></i>Utilisateurs</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/statistics.php"><i
                                        class="bi bi-bar-chart me-2"></i>Statistiques</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/messages.php"><i
                                        class="bi bi-envelope me-2"></i>Messages</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>

                <!-- Partie droite: messages et profil utilisateur -->
                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- Icône messages avec badge de notification -->
                    <li class="nav-item me-3">
                        <?php
                    // Détermination de la page de messages selon le rôle
                    $msg_pages = ['admin'=>'admin','manager'=>'production','employe'=>'production','client'=>'ventes'];
                    $msg_url = BASE_URL . ($msg_pages[$role] ?? 'admin') . '/messages.php';
                    ?>
                        <a class="nav-link position-relative" href="<?= $msg_url ?>">
                            <i class="bi bi-envelope"></i>
                            <?php if ($unread_count > 0): ?>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $unread_count ?>
                            </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <!-- Menu profil utilisateur -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <?= htmlspecialchars($_SESSION['user']['nom']) ?>
                            <span class="badge bg-light text-dark ms-1"><?= htmlspecialchars($role) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>auth/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </a></li>
                        </ul>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Conteneur principal -->
    <div class="container">
        <?php 
// Affichage du message de succès (vert)
if ($flash_success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($flash_success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php 
// Affichage du message d'erreur (rouge)
if ($flash_error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($flash_error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>