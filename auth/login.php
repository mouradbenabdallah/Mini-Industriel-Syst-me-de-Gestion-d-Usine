<?php
/**
 * Page de connexion utilisateur
 * Permet aux utilisateurs existants de se connecter au système
 * 
 * Projet: Usine Industriel - Système de gestion d'usine
 * Mode: Procédural (sans POO)
 */

// Démarrage de la session pour gérer les données de l'utilisateur connecté
session_start();

// Vérification: si l'utilisateur est déjà connecté, redirection vers la page d'accueil
if (isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

// Récupération d'un message d'erreur éventuelle (stocké en session lors d'un échec de connexion)
$error = $_SESSION['error'] ?? null;
// Suppression du message d'erreur de la session après l'avoir récupéré
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Usine Industriel</title>
    <!-- Bootstrap CSS pour le style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons pour les icônes -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Feuille de style personnalisée -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <!-- En-tête de la page avec logo et titre -->
                <div class="text-center mb-4">
                    <i class="bi bi-gear-fill text-success" style="font-size:3rem;"></i>
                    <h2 class="mt-2 fw-bold">Usine Industriel</h2>
                    <p class="text-muted">Connectez-vous à votre espace</p>
                </div>
                <!-- Carte de connexion -->
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-lock"></i> Connexion</h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Affichage du message d'erreur si existant -->
                        <?php if ($error): ?>
                        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i>
                            <?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <!-- Formulaire de connexion -->
                        <form method="post" action="login_process.php">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required autofocus
                                    placeholder="votre@email.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" name="password" class="form-control" required
                                    placeholder="mot de passe">
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-box-arrow-in-right"></i> Se connecter
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center text-muted small">
                        Pas encore de compte ?
                        <a href="register.php" class="text-success">Créer un compte client</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS pour les fonctionnalités interactives -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>