<?php
/**
 * Traitement de la connexion utilisateur
 * Vérifie les identifiants et crée la session utilisateur
 * 
 * Projet: Usine Industriel - Système de gestion d'usine
 * Mode: Procédural (sans POO)
 */

// Démarrage de la session
session_start();

// Inclusion du fichier de configuration de la base de données
// Cela nous donne accès à la variable $pdo pour les requêtes SQL
require_once '../config/database.php';

// Vérification: seul les requêtes POST sont acceptées (sécurité contre les accès directs)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Récupération et nettoyage des données du formulaire
$email    = trim($_POST['email'] ?? '');      // Email nettoyé (suppression des espaces)
$password = $_POST['password'] ?? '';         // Mot de passe tel quel

// Vérification: tous les champs doivent être remplis
if (!$email || !$password) {
    // Stockage du message d'erreur en session pour affichage sur la page de connexion
    $_SESSION['error'] = 'Veuillez remplir tous les champs.';
    header('Location: login.php');
    exit;
}

// Préparation de la requête SQL pour trouver l'utilisateur par email
// La requête cherche un utilisateur actif (actif = 1)
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND actif = 1');
// Exécution de la requête avec l'email comme paramètre
$stmt->execute([$email]);
// Récupération de l'utilisateur sous forme de tableau associatif
$user = $stmt->fetch();

// Vérification des identifiants:
// - L'utilisateur doit exister dans la base de données
// - Le mot de passe doit correspondre (dans un vrai projet, utiliser password_hash et password_verify)
if (!$user || $user['password'] !== $password) {
    // Message d'erreur générique pour des raisons de sécurité
    $_SESSION['error'] = 'Email ou mot de passe incorrect.';
    header('Location: login.php');
    exit;
}

// Création de la session utilisateur avec les informations nécessaires
$_SESSION['user'] = [
    'id'    => $user['id'],           // ID unique de l'utilisateur
    'nom'   => $user['nom'],           // Nom complet
    'email' => $user['email'],        // Adresse email
    'role'  => $user['role'],         // Rôle (admin, manager, employe, client)
];

// Redirection vers la page d'accueil après connexion réussie
header('Location: ../index.php');
exit;