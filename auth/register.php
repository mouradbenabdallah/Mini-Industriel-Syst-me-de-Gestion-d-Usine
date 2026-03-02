<?php
/**
 * Page d'inscription utilisateur
 * Permet aux nouveaux clients de cr√©er un compte
 * 
 * Projet: Usine Industriel - Syst√®me de gestion d'usine
 * Mode: Proc√©dural (sans POO)
 */

// D√©marrage de la session pour g√©rer les donn√©es utilisateur
session_start();

// V√©rification: si l'utilisateur est d√©j√† connect√©, redirection vers l'accueil
if (isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

// R√©cup√©ration d'un message d'erreur √©ventuelle (stock√© en session)
$error = $_SESSION['error'] ?? null;
// Suppression du message d'erreur de la session
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription ‚TNDî Usine Industriel</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Style personnalis√© -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <!-- En-t√™te avec logo -->
                <div class="text-center mb-4">
                    <i class="bi bi-gear-fill text-success" style="font-size:3rem;"></i>
                    <h2 class="mt-2 fw-bold">Usine Industriel</h2>
                    <p class="text-muted">Cr√©er un compte client</p>
                </div>
                <!-- Carte d'inscription -->
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-person-plus"></i> Inscription</h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Affichage des erreurs -->
                        <?php if ($error): ?>
                        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i>
                            <?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <!-- Formulaire d'inscription -->
                        <form method="post" action="register_process.php">
                            <div class="mb-3">
                                <label class="form-label">Nom complet</label>
                                <input type="text" name="nom" class="form-control" required autofocus
                                    placeholder="Votre nom">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required
                                    placeholder="votre@email.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" name="password" class="form-control" required
                                    placeholder="‚TND¢‚TND¢‚TND¢‚TND¢‚TND¢‚TND¢‚TND¢‚TND¢" minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmer le mot de passe</label>
                                <input type="password" name="password2" class="form-control" required
                                    placeholder="‚TND¢‚TND¢‚TND¢‚TND¢‚TND¢‚TND¢‚TND¢‚TND¢">
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-person-check"></i> Cr√©er mon compte
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center text-muted small">
                        D√©j√† inscrit ? <a href="login.php" class="text-success">Se connecter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>