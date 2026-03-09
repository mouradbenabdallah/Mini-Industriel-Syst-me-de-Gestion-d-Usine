<?php
/**
 * Page d'inscription - Usine Industriel
 * Utilise uniquement Bootstrap 5 (pas de CSS personnalisé)
 */

// Démarrage de la session
session_start();

// Si déjà connecté → aller au tableau de bord
if (isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

// Récupérer le message d'erreur stocké en session (s'il existe)
$error = $_SESSION['error'] ?? null;

// Supprimer le message d'erreur après l'avoir lu
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — Usine Industriel</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts – même police que le reste de l'app -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏭</text></svg>">

    <style>
        /* Appliquer la même police que le reste de l'application */
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<!--
    Même fond dégradé bleu que la page de connexion.
    d-flex min-vh-100 align-items-center justify-content-center = centrage total
    py-4 = un peu d'espace vertical (utile si le formulaire est long)
-->
<body class="d-flex min-vh-100 align-items-center justify-content-center py-4"
      style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9);">

    <!-- Conteneur centré, largeur max 460px (un peu plus large car plus de champs) -->
    <div class="w-100 px-3" style="max-width: 460px;">

        <!-- ── LOGO AU-DESSUS DE LA CARTE ── -->
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center
                        bg-white bg-opacity-25 rounded-4 mb-3"
                 style="width:72px; height:72px; font-size:2rem; color:#fff;">
                <i class="bi bi-gear-fill"></i>
            </div>
            <h1 class="text-white fw-bold fs-3 mb-1">Usine Industriel</h1>
            <p class="text-white-50 small mb-0">Créer un compte client</p>
        </div>

        <!-- ── CARTE D'INSCRIPTION ── -->
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body p-4 p-md-5">

                <!-- Titre de la carte -->
                <h2 class="fw-bold fs-4 mb-1">
                    <i class="bi bi-person-plus text-primary me-2"></i>Inscription
                </h2>
                <p class="text-muted small mb-4">
                    Remplissez les champs ci-dessous pour créer votre compte
                </p>

                <!-- Message d'erreur (ex: email déjà utilisé, mots de passe différents) -->
                <?php if ($error): ?>
                <div class="alert alert-danger border-0 rounded-3 py-2 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <!-- ── FORMULAIRE D'INSCRIPTION ── -->
                <form method="post" action="register_process.php" novalidate>

                    <!-- Champ: Nom complet -->
                    <div class="mb-3">
                        <label for="nom" class="form-label fw-semibold small">
                            <i class="bi bi-person text-primary me-1"></i>Nom complet
                        </label>
                        <input type="text" name="nom" id="nom"
                               class="form-control rounded-3"
                               placeholder="Votre nom et prénom"
                               required autofocus>
                    </div>

                    <!-- Champ: Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold small">
                            <i class="bi bi-envelope text-primary me-1"></i>Adresse email
                        </label>
                        <input type="email" name="email" id="email"
                               class="form-control rounded-3"
                               placeholder="votre@email.com"
                               required>
                    </div>

                    <!-- Champ: Mot de passe -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold small">
                            <i class="bi bi-lock text-primary me-1"></i>Mot de passe
                        </label>
                        <input type="password" name="password" id="password"
                               class="form-control rounded-3"
                               placeholder="Minimum 6 caractères"
                               minlength="6" required>
                    </div>

                    <!-- Champ: Confirmation du mot de passe -->
                    <div class="mb-4">
                        <label for="password2" class="form-label fw-semibold small">
                            <i class="bi bi-lock-fill text-primary me-1"></i>Confirmer le mot de passe
                        </label>
                        <input type="password" name="password2" id="password2"
                               class="form-control rounded-3"
                               placeholder="Répétez votre mot de passe"
                               required>
                    </div>

                    <!-- Bouton de soumission -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-semibold">
                            <i class="bi bi-person-check me-2"></i>Créer mon compte
                        </button>
                    </div>
                </form>

            </div>

            <!-- Pied de la carte: lien vers la connexion -->
            <div class="card-footer bg-light border-0 rounded-bottom-4 text-center py-3">
                <span class="text-muted small">
                    Déjà inscrit ?
                    <a href="login.php" class="text-primary fw-semibold text-decoration-none">
                        Se connecter
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