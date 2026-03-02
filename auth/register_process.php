<?php
/**
 * Traitement de l'inscription utilisateur
 * Crée un nouveau compte client dans la base de données
 * 
 * Projet: Usine Industriel - Système de gestion d'usine
 * Mode: Procédural (sans POO)
 */

// Démarrage de la session
session_start();

// Inclusion de la configuration de la base de données
require_once '../config/database.php';

// Vérification: seul les requêtes POST sont acceptées
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// Récupération et nettoyage des données du formulaire
$nom       = trim($_POST['nom'] ?? '');       // Nom nettoyé
$email     = trim($_POST['email'] ?? '');     // Email nettoyé
$password  = $_POST['password'] ?? '';        // Mot de passe
$password2 = $_POST['password2'] ?? '';       // Confirmation du mot de passe

// Vérification: tous les champs doivent être remplis
if (!$nom || !$email || !$password) {
    $_SESSION['error'] = 'Veuillez remplir tous les champs.';
    header('Location: register.php');
    exit;
}

// Vérification: les deux mots de passe doivent être identiques
if ($password !== $password2) {
    $_SESSION['error'] = 'Les mots de passe ne correspondent pas.';
    header('Location: register.php');
    exit;
}

// Vérification: le mot de passe doit contenir au moins 6 caractères
if (strlen($password) < 6) {
    $_SESSION['error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
    header('Location: register.php');
    exit;
}

// Vérification: l'email n'existe pas déjà dans la base de données
// Préparation de la requête de recherche
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);

// Si un utilisateur existe déjà avec cet email
if ($stmt->fetch()) {
    $_SESSION['error'] = 'Cet email est déjà utilisé.';
    header('Location: register.php');
    exit;
}

// Insertion du nouvel utilisateur dans la base de données
// Le rôle par défaut est 'client'
$stmt = $pdo->prepare('INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, \'client\')');
$stmt->execute([$nom, $email, $password]);

// Message de succès et redirection vers la page de connexion
$_SESSION['success'] = 'Compte créé avec succès. Vous pouvez maintenant vous connecter.';
header('Location: login.php');
exit;