<?php
/**
 * Liste des produits en stock
 * Affiche tous les produits avec leurs quantités et alertes de stock bas
 * 
 * Fonctionnalités:
 * - Liste de tous les produits
 * - Filtrage par catégorie (matière première / produit fini)
 * - Indicateur visuel de stock bas
 * - Actions: voir, modifier, supprimer
 * 
 * Projet: Usine Industriel - Système de gestion d'usine
 * Mode: Procédural (sans POO)
 */

// Démarrage de la session
session_start();

// Rôles autorisés: admin, manager et employé peuvent voir le stock
$allowed_roles = ['admin','manager','employe'];

// Inclusion de la configuration de la base de données
require_once '../config/database.php';

// Inclusion du fichier de vérification d'authentification
require_once '../includes/auth_check.php';

// Récupération du paramètre de catégorie depuis l'URL
$categorie = $_GET['categorie'] ?? '';

// Catégories valides
$valid_cats = ['matiere_premiere','produit_fini',''];

// Validation de la catégorie (sécurité)
if (!in_array($categorie, $valid_cats)) $categorie = '';

// Requête SQL selon la catégorie sélectionnée
if ($categorie) {
    // Si une catégorie est sélectionnée, filtrer par catégorie
    $stmt = $pdo->prepare('SELECT * FROM produits WHERE categorie=? ORDER BY nom');
    $stmt->execute([$categorie]);
} else {
    // Sinon, afficher tous les produits
    $stmt = $pdo->query('SELECT * FROM produits ORDER BY nom');
}
$produits = $stmt->fetchAll();

// Configuration du titre de la page
$page_title   = 'Gestion du stock';
$module_color = 'warning';

// Inclusion de l'en-tête commun
require_once '../includes/header.php';
?>
<!-- En-tête avec titre et bouton d'ajout -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam"></i> Stock</h2>
    <!-- Bouton ajouter pour admin et manager -->
    <?php if (hasAnyRole(['admin','manager'])): ?>
    <a href="create.php" class="btn btn-warning"><i class="bi bi-plus-lg"></i> Ajouter un produit</a>
    <?php endif; ?>
</div>

<!-- Filtres par catégorie -->
<div class="mb-3">
    <div class="btn-group">
        <a href="index.php" class="btn btn-outline-secondary <?= !$categorie ? 'active' : '' ?>">Tous</a>
        <a href="index.php?categorie=matiere_premiere"
            class="btn btn-outline-secondary <?= $categorie==='matiere_premiere' ? 'active' : '' ?>">Matières
            premières</a>
        <a href="index.php?categorie=produit_fini"
            class="btn btn-outline-secondary <?= $categorie==='produit_fini' ? 'active' : '' ?>">Produits finis</a>
    </div>
    <a href="historique.php" class="btn btn-outline-dark ms-2"><i class="bi bi-clock-history"></i> Historique</a>
    <a href="mouvement.php" class="btn btn-outline-warning ms-2"><i class="bi bi-arrow-left-right"></i> Mouvement</a>
</div>

<!-- Tableau des produits -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-warning">
                <tr>
                    <th>Produit</th>
                    <th>Catégorie</th>
                    <th>Qté</th>
                    <th>Qté min.</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $p): ?>
                <!-- Classe CSS conditionnelle pour les stocks bas -->
                <tr class="<?= $p['quantite'] <= $p['quantite_min'] ? 'table-low-stock' : '' ?>">
                    <td>
                        <?= htmlspecialchars($p['nom']) ?>
                        <!-- Badge d'alerte si stock bas -->
                        <?php if ($p['quantite'] <= $p['quantite_min']): ?>
                        <span class="badge bg-danger ms-1"><i class="bi bi-exclamation-triangle"></i> Stock bas</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $p['categorie'] === 'matiere_premiere' ? 'Matière première' : 'Produit fini' ?></td>
                    <td><strong><?= $p['quantite'] ?></strong></td>
                    <td><?= $p['quantite_min'] ?></td>
                    <td><?= number_format($p['prix'], 2, ',', ' ') ?> TND</td>
                    <td>
                        <!-- Bouton voir -->
                        <a href="view.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary"><i
                                class="bi bi-eye"></i></a>
                        <!-- Boutons modifier et supprimer pour admin et manager -->
                        <?php if (hasAnyRole(['admin','manager'])): ?>
                        <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-warning"><i
                                class="bi bi-pencil"></i></a>
                        <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Supprimer ce produit ?')"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$produits): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">Aucun produit trouvé.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
// Inclusion du pied de page
require_once '../includes/footer.php'; 
?>