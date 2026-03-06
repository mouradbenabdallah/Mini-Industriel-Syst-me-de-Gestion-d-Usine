<?php
/**
 * Formulaire d'ajout d'un produit au stock
 * Permet aux admins et managers d'ajouter un nouveau produit
 * 
 * Projet: Usine Industriel - Système de gestion d'usine
 * Mode: Procédural (sans POO)
 */

// Démarrage de la session
session_start();

// Rôles autorisés: seul admin et manager peuvent ajouter des produits
$allowed_roles = ['admin','manager'];

// Inclusion de la configuration de la base de données
require_once '../config/database.php';

// Inclusion du fichier de vérification d'authentification
require_once '../includes/auth_check.php';

// Configuration du titre de la page
$page_title   = 'Ajouter un produit';
$module_color = 'warning';

// Inclusion de l'en-tête commun
require_once '../includes/header.php';
?>
<!-- En-tête avec titre et bouton retour -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> Ajouter un produit</h2>
    <a href="index.php" class="btn btn-outline-warning"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

<!-- Formulaire d'ajout de produit -->
<div class="card">
    <div class="card-body">
        <form method="post" action="create_process.php">
            <div class="row g-3">
                <!-- Nom du produit -->
                <div class="col-md-6">
                    <label class="form-label">Nom du produit</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <!-- Catégorie -->
                <div class="col-md-6">
                    <label class="form-label">Catégorie</label>
                    <select name="categorie" class="form-select" required>
                        <option value="matiere_premiere">Matière première</option>
                        <option value="produit_fini">Produit fini</option>
                    </select>
                </div>
                <!-- Quantité initiale -->
                <div class="col-md-4">
                    <label class="form-label">Quantité initiale</label>
                    <input type="number" name="quantite" class="form-control" min="0" step="0.01" value="0" required>
                </div>
                <!-- Quantité minimum (seuil d'alerte) -->
                <div class="col-md-4">
                    <label class="form-label">Quantité minimum (alerte)</label>
                    <input type="number" name="quantite_min" class="form-control" min="0" step="0.01" value="5"
                        required>
                </div>
                <!-- Prix unitaire -->
                <div class="col-md-4">
                    <label class="form-label">Prix unitaire (TND)</label>
                    <input type="number" name="prix" class="form-control" min="0" step="0.01" value="0.00" required>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php 
// Inclusion du pied de page
require_once '../includes/footer.php'; 
?>