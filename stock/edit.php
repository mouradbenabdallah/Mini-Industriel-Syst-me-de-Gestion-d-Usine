<?php
/**
 * Formulaire de modification d'un produit
 * Permet de modifier les détails d'un produit existant
 * 
 * Projet: Usine Industriel - Système de gestion d'usine
 * Mode: Procédural (sans POO)
 */

// Démarrage de la session
session_start();

// Rôles autorisés: admin et manager seulement
$allowed_roles = ['admin','manager'];

// Inclusion de la configuration de la base de données
require_once '../config/database.php';

// Inclusion du fichier de vérification d'authentification
require_once '../includes/auth_check.php';

// Récupération de l'ID du produit depuis l'URL
$id = (int)($_GET['id'] ?? 0);

// Requête pour récupérer les détails du produit
$stmt = $pdo->prepare('SELECT * FROM produits WHERE id=?');
$stmt->execute([$id]);
$produit = $stmt->fetch();

// Vérification: le produit doit exister
if (!$produit) {
    $_SESSION['error'] = 'Produit introuvable.';
    header('Location: index.php');
    exit;
}

// Configuration du titre de la page
$page_title   = 'Modifier produit';
$module_color = 'warning';

// Inclusion de l'en-tête commun
require_once '../includes/header.php';
?>
<!-- En-tête avec titre et bouton retour -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Modifier le produit <?= htmlspecialchars($produit['nom']) ?></h2>
    <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

<!-- Formulaire de modification -->
<div class="card">
    <div class="card-body">
        <form method="post" action="edit_process.php">
            <!-- Champ caché pour l'ID -->
            <input type="hidden" name="id" value="<?= $produit['id'] ?>">

            <div class="row g-3">
                <!-- Nom du produit -->
                <div class="col-md-6">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" required
                        value="<?= htmlspecialchars($produit['nom']) ?>">
                </div>
                <!-- Catégorie -->
                <div class="col-md-6">
                    <label class="form-label">Catégorie</label>
                    <select name="categorie" class="form-select" required>
                        <option value="matiere_premiere"
                            <?= $produit['categorie']==='matiere_premiere'?'selected':'' ?>>Matière première</option>
                        <option value="produit_fini" <?= $produit['categorie']==='produit_fini'?'selected':'' ?>>Produit
                            fini</option>
                    </select>
                </div>
                <!-- Quantité minimum -->
                <div class="col-md-4">
                    <label class="form-label">Quantité min. (alerte)</label>
                    <input type="number" name="quantite_min" class="form-control" min="0" step="0.01" required
                        value="<?= $produit['quantite_min'] ?>">
                </div>
                <!-- Prix unitaire -->
                <div class="col-md-4">
                    <label class="form-label">Prix unitaire (TND)</label>
                    <input type="number" name="prix" class="form-control" min="0" step="0.01" required
                        value="<?= $produit['prix'] ?>">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php 
// Inclusion du pied de page
require_once '../includes/footer.php'; 
?>