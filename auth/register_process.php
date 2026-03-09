<?php
/**
 * ============================================================
 * FICHIER: auth/register_process.php
 * DESCRIPTION: Traitement du formulaire d'inscription
 * PROJET: Usine Industriel - Système de gestion d'usine
 * ============================================================
 *
 * Ce fichier reçoit les données du formulaire d'inscription,
 * les vérifie, et crée le nouveau compte dans la base de données.
 *
 * IMPORTANT: Ce fichier n'affiche rien. Il traite les données
 * et redirige l'utilisateur vers une autre page.
 */

// Démarrage de la session (obligatoire pour stocker les messages flash)
session_start();

// Inclusion de la configuration de la base de données
// Cela nous donne accès à la variable $pdo pour faire des requêtes SQL
require_once '../config/database.php';

// ============================================================
// ÉTAPE 1: VÉRIFIER QUE LA PAGE EST APPELÉE DEPUIS UN FORMULAIRE
// ============================================================
// On accepte UNIQUEMENT les requêtes de type POST (envoi de formulaire).
// Si quelqu'un tape directement l'URL dans son navigateur (GET),
// on le redirige vers la page d'inscription.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit; // Arrêter le script pour éviter d'exécuter la suite
}

// ============================================================
// ÉTAPE 2: RÉCUPÉRER ET NETTOYER LES DONNÉES DU FORMULAIRE
// ============================================================
// trim() supprime les espaces au début et à la fin d'une chaîne
// L'opérateur ?? retourne 'chaîne vide' si la variable n'existe pas
$nom       = trim($_POST['nom'] ?? '');       // Nom complet de l'utilisateur
$email     = trim($_POST['email'] ?? '');     // Adresse email
$password  = $_POST['password'] ?? '';        // Mot de passe (pas besoin de trim sur les mots de passe)
$password2 = $_POST['password2'] ?? '';       // Confirmation du mot de passe

// ============================================================
// ÉTAPE 3: VALIDER LES DONNÉES (VÉRIFICATIONS)
// ============================================================

// Vérification 1: Tous les champs obligatoires sont remplis
if (!$nom || !$email || !$password || !$password2) {
    $_SESSION['error'] = 'Veuillez remplir tous les champs.';
    header('Location: register.php');
    exit;
}

// Vérification 2: L'email a un format valide (contient un @, un point, etc.)
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'L\'adresse email n\'est pas valide.';
    header('Location: register.php');
    exit;
}

// Vérification 3: Les deux mots de passe sont identiques
if ($password !== $password2) {
    $_SESSION['error'] = 'Les mots de passe ne correspondent pas.';
    header('Location: register.php');
    exit;
}

// Vérification 4: Le mot de passe est assez long (minimum 6 caractères)
if (strlen($password) < 6) {
    $_SESSION['error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
    header('Location: register.php');
    exit;
}

// Vérification 5: L'email n'est pas déjà utilisé par un autre compte
// On prépare une requête pour chercher un utilisateur avec cet email
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]); // On passe l'email comme paramètre sécurisé

// Si un résultat est trouvé, un compte existe déjà avec cet email
if ($stmt->fetch()) {
    $_SESSION['error'] = 'Cet email est déjà utilisé par un autre compte.';
    header('Location: register.php');
    exit;
}

// ============================================================
// ÉTAPE 4: SÉCURISER LE MOT DE PASSE AVANT SAUVEGARDE
// ============================================================
// IMPORTANT - SÉCURITÉ:
// On ne sauvegarde JAMAIS un mot de passe en clair dans la base de données.
// password_hash() transforme le mot de passe en un code illisible (hash bcrypt).
// Ex: "monmotdepasse123" devient "$2y$10$xK8xK8x..."
// Même si quelqu'un vole la base de données, il ne peut pas lire les mots de passe.
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// ============================================================
// ÉTAPE 5: ENREGISTRER LE NOUVEAU COMPTE EN BASE DE DONNÉES
// ============================================================
// On prépare la requête SQL d'insertion
// Les ? sont des paramètres qui seront remplacés de façon sécurisée
// Cela prévient les attaques SQL Injection
$stmt = $pdo->prepare('INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, \'client\')');

// On exécute la requête avec les valeurs réelles
// Le rôle par défaut pour les inscriptions publiques est 'client'
$stmt->execute([$nom, $email, $hashedPassword]);

// ============================================================
// ÉTAPE 6: REDIRECTION VERS LA PAGE DE CONNEXION
// ============================================================
// Stockage du message de succès en session (sera affiché sur la page de connexion)
$_SESSION['success'] = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
header('Location: login.php');
exit;