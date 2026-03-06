<?php
/**
 * Traitement de l'ajout d'un produit au stock
 * Insère un nouveau produit dans la base de données
 * 
 * Projet: Usine Industriel - Système de gestion d'usine
 * Mode: Procédural (sans POO)
 */

// Démarrage de la session
session_start();

// Rôles autorisés
$allowed_roles = ['admin','manager'];

// Inclusion de la configuration de la base de données
require_once '../config/database.php';

// Inclusion du fichier de vérification d'authentification
require_once '../includes/auth_check.php';

// Vérification: seul les requêtes POST sont acceptées
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create.php');
    exit;
}

// Récupération et nettoyage des données du formulaire
$nom          = trim($_POST['nom'] ?? '');             // Nom du produit
$categorie    = $_POST['categorie'] ?? '';             // Catégorie (matiere_premiere ou produit_fini)
$quantite     = (float)($_POST['quantite'] ?? 0);     // Quantité initiale
$quantite_min = (float)($_POST['quantite_min'] ?? 5);  // Seuil d'alerte stock bas
$prix         = (float)($_POST['prix'] ?? 0);         // Prix unitaire

// Validation des données
if (!$nom || !in_array($categorie, ['matiere_premiere','produit_fini'])) {
    $_SESSION['error'] = 'Données invalides.';
    header('Location: create.php');
    exit;
}

// Insertion du nouveau produit dans la base de données
$stmt = $pdo->prepare(
    'INSERT INTO produits (nom, categorie, quantite, quantite_min, prix) VALUES (?,?,?,?,?)'
);
$stmt->execute([$nom, $categorie, $quantite, $quantite_min, $prix]);

// Message de succès et redirection vers la liste des produits
$_SESSION['success'] = 'Produit créé avec succès.';
header('Location: index.php');
exit;