<?php
// ========================================
// HEADER SIMPLIFIÉ - POUR DÉBUTANTS
// ========================================

// Démarrer la session si pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion à la base de données
require_once __DIR__ . "/../config/database.php";

// Récupérer l'utilisateur connecté
$user = $_SESSION["user"] ?? null;
$role = $user["role"] ?? "";

// Compter les messages non lus (requête SIMPLE sans jointure)
$unread_count = 0;
if ($user) {
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM messages WHERE destinataire_id = ? AND lu = 0",
    );
    $stmt->execute([$user["id"]]);
    $unread_count = $stmt->fetchColumn();
}

// Messages flash
$success_message = $_SESSION["success"] ?? null;
$error_message = $_SESSION["error"] ?? null;
unset($_SESSION["success"], $_SESSION["error"]);

// Titre de la page
$page_title = $page_title ?? "Usine Industriel";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Usine Industriel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- NAVIGATION -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="../index.php">
            <i class="bi bi-gear-fill"></i> Usine Industriel
        </a>

        <!-- Bouton mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if ($role == "admin" || $role == "manager"): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../production/index.php">
                            <i class="bi bi-gear"></i> Production
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../stock/index.php">
                            <i class="bi bi-box"></i> Stock
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../rh/employes.php">
                            <i class="bi bi-people"></i> RH
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../ventes/commandes.php">
                            <i class="bi bi-cart"></i> Ventes
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role == "employe"): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../production/index.php">
                            <i class="bi bi-gear"></i> Mes Tâches
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../rh/conges.php">
                            <i class="bi bi-calendar"></i> Mes Congés
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role == "client"): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../stock/index.php">
                            <i class="bi bi-box"></i> Produits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../ventes/commandes.php">
                            <i class="bi bi-cart"></i> Mes Commandes
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Utilisateur -->
            <ul class="navbar-nav">
                <?php if ($user): ?>
                    <!-- Messages -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="../admin/messages.php">
                            <i class="bi bi-envelope"></i>
                            <?php if ($unread_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $unread_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <!-- Menu utilisateur -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo $user[
                                "nom"
                            ]; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../dashboard/<?php echo $role; ?>_dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a></li>
                            <?php if ($role == "admin"): ?>
                                <li><a class="dropdown-item" href="../admin/users.php">
                                    <i class="bi bi-people"></i> Utilisateurs
                                </a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php">
                                <i class="bi bi-box-arrow-right"></i> Déconnexion
                            </a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Messages flash -->
<div class="container mt-3">
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>

<!-- Contenu de la page -->
<div class="container my-4">
