<?php
/**
 * Page de connexion - Usine Industriel
 * Utilise uniquement Bootstrap 5 (pas de CSS personnalisé)
 */

// Démarrage de la session
session_start();

// Si déjà connecté → aller au tableau de bord
if (isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

// Récupérer les messages flash (erreur ou succès) stockés en session
$error   = $_SESSION['error']   ?? null;
$success = $_SESSION['success'] ?? null;

// Supprimer les messages après les avoir lus (ils ne doivent s'afficher qu'une fois)
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Usine Industriel</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts – même police que le reste de l'app -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏭</text></svg>">

    <style>
    /* Appliquer la même police que le reste de l'application */
    * {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    </style>
</head>

<!--
    Fond en dégradé bleu avec Bootstrap:
    - d-flex min-vh-100 align-items-center justify-content-center → centrer verticalement et horizontalement
    - Le style inline gère le dégradé (Bootstrap n'a pas de classes de dégradé built-in)
-->

<body class="d-flex min-vh-100 align-items-center justify-content-center"
    style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9);">

    <!-- Conteneur centré, largeur max 420px -->
    <div class="w-100 px-3" style="max-width: 420px;">

        <!-- ── LOGO AU-DESSUS DE LA CARTE ── -->
        <div class="text-center mb-4">
            <!-- Icône engrenage dans un carré Bootstrap arrondi -->
            <div class="d-inline-flex align-items-center justify-content-center
                        bg-white bg-opacity-25 rounded-4 mb-3"
                style="width:72px; height:72px; font-size:2rem; color:#fff;">
                <i class="bi bi-gear-fill"></i>
            </div>
            <h1 class="text-white fw-bold fs-3 mb-1">Usine Industriel</h1>
            <p class="text-white-50 small mb-0">Système de Gestion d'Usine</p>
        </div>

        <!-- ── CARTE DE CONNEXION ── -->
        <!-- shadow-lg = ombre forte, rounded-4 = coins très arrondis, border-0 = pas de bordure -->
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body p-4 p-md-5">

                <!-- Titre de la carte -->
                <h2 class="fw-bold fs-4 mb-1">
                    <i class="bi bi-box-arrow-in-right text-primary me-2"></i>Connexion
                </h2>
                <p class="text-muted small mb-4">
                    Entrez vos identifiants pour accéder à votre espace
                </p>

                <!-- Message d'erreur (mauvais email/mot de passe) -->
                <?php if ($error): ?>
                <div class="alert alert-danger border-0 rounded-3 py-2 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <!-- Message de succès (après une inscription réussie) -->
                <?php if ($success): ?>
                <div class="alert alert-success border-0 rounded-3 py-2 small" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
                <?php endif; ?>

                <!-- ── FORMULAIRE ── -->
                <!-- method="post" = données envoyées de façon sécurisée (pas visibles dans l'URL) -->
                <form method="post" action="login_process.php" novalidate>

                    <!-- Champ Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold small">
                            <i class="bi bi-envelope text-primary me-1"></i>Adresse email
                        </label>
                        <input type="email" name="email" id="email" class="form-control rounded-3"
                            placeholder="votre@email.com" required autofocus>
                    </div>

                    <!-- Champ Mot de passe -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold small">
                            <i class="bi bi-lock text-primary me-1"></i>Mot de passe
                        </label>
                        <input type="password" name="password" id="password" class="form-control rounded-3"
                            placeholder="••••••••" required>
                    </div>

                    <!-- Bouton de soumission - btn-primary = bleu Bootstrap -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                        </button>
                    </div>
                </form>

            </div>

            <!-- Pied de la carte: lien vers l'inscription -->
            <div class="card-footer bg-light border-0 rounded-bottom-4 text-center py-3">
                <span class="text-muted small">
                    Pas encore de compte ?
                    <a href="register.php" class="text-primary fw-semibold text-decoration-none">
                        Créer un compte
                    </a>
                </span>
            </div>
        </div>
        <!-- fin .card -->

    </div>
    <!-- fin conteneur -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>