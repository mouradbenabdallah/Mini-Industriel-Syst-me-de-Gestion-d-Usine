<?php
/**
 * Traitement de la modification d'un produit
 * Met à jour les informations d'un produit existant
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

// Vérification: seul les requêtes POST sont acceptées
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Récupération et nettoyage des données du formulaire
$id           = (int)($_POST['id'] ?? 0);              // ID du produit
$nom          = trim($_POST['nom'] ?? '');             // Nouveau nom
$categorie    = $_POST['categorie'] ?? '';             // Nouvelle catégorie
$quantite_min = (float)($_POST['quantite_min'] ?? 5);  // Nouveau seuil
$prix         = (float)($_POST['prix'] ?? 0);         // Nouveau prix

// Validation des données
if (!$id || !$nom || !in_array($categorie, ['matiere_premiere','produit_fini'])) {
    $_SESSION['error'] = 'Données invalides.';
    header('Location: edit.php?id=' . $id);
    exit;
}

// Mise à jour du produit dans la base de données
$stmt = $pdo->prepare(
    'UPDATE produits SET nom=?, categorie=?, quantite_min=?, prix=? WHERE id=?'
);
$stmt->execute([$nom, $categorie, $quantite_min, $prix, $id]);

// Message de succès et redirection vers la page de vue
$_SESSION['success'] = 'Produit mis à jour.';
header('Location: view.php?id=' . $id);
exit;