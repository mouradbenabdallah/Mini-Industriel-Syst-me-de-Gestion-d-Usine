<?php
/**
 * ============================================================
 * FICHIER: auth/login_process.php
 * DESCRIPTION: Traitement du formulaire de connexion
 * PROJET: Usine Industriel - Système de gestion d'usine
 * ============================================================
 *
 * Ce fichier reçoit les identifiants (email + mot de passe),
 * vérifie qu'ils sont corrects, et crée une session utilisateur.
 *
 * IMPORTANT: Ce fichier n'affiche rien. Il traite les données
 * et redirige l'utilisateur vers une autre page.
 */

// Démarrage de la session (obligatoire pour stocker les informations de l'utilisateur)
session_start();

// Inclusion de la configuration de la base de données
// Cela nous donne accès à la variable $pdo pour faire des requêtes SQL
require_once '../config/database.php';

// ============================================================
// ÉTAPE 1: VÉRIFIER QUE LA PAGE EST APPELÉE DEPUIS UN FORMULAIRE
// ============================================================
// On accepte UNIQUEMENT les requêtes de type POST (envoi de formulaire).
// Si quelqu'un tape directement l'URL dans le navigateur, on le redirige.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit; // Arrêter le script immédiatement
}

// ============================================================
// ÉTAPE 2: RÉCUPÉRER ET NETTOYER LES DONNÉES DU FORMULAIRE
// ============================================================
// trim() supprime les espaces inutiles au début et à la fin
$email    = trim($_POST['email'] ?? '');  // Email de l'utilisateur
$password = $_POST['password'] ?? '';     // Mot de passe (pas de trim — les espaces peuvent être voulus)

// ============================================================
// ÉTAPE 3: VÉRIFIER QUE LES CHAMPS SONT REMPLIS
// ============================================================
// Si l'un des champs est vide, on redirige avec un message d'erreur
if (!$email || !$password) {
    $_SESSION['error'] = 'Veuillez remplir tous les champs.';
    header('Location: login.php');
    exit;
}

// ============================================================
// ÉTAPE 4: CHERCHER L'UTILISATEUR EN BASE DE DONNÉES
// ============================================================
// On cherche un utilisateur avec cet email ET dont le compte est actif (actif = 1)
// Les requêtes préparées avec ? protègent contre les attaques SQL Injection
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND actif = 1');
$stmt->execute([$email]); // Exécution avec l'email comme paramètre

// fetch() récupère la première (et normalement unique) ligne du résultat
// Si aucun utilisateur trouvé, $user sera false
$user = $stmt->fetch();

// ============================================================
// ÉTAPE 5: VÉRIFIER LE MOT DE PASSE
// ============================================================
// IMPORTANT - SÉCURITÉ:
// password_verify() compare le mot de passe saisi par l'utilisateur
// avec le hash stocké en base de données (créé lors de l'inscription).
// On ne compare JAMAIS les mots de passe directement en texte clair !
//
// Exemple de ce qu'on compare:
//   Saisi: "monmotdepasse123"
//   En base: "$2y$10$xK8xK8x..." (hash illisible mais vérifiable)
//
// Si l'utilisateur n'existe pas ($user est false), on saute la vérification.
$motDePasseCorrect = $user && password_verify($password, $user['password']);

// Si l'utilisateur n'existe pas OU si le mot de passe est incorrect
if (!$motDePasseCorrect) {
    // On utilise un message générique pour des raisons de sécurité.
    // On ne dit pas "email incorrect" ou "mot de passe incorrect" séparément,
    // car cela aiderait un attaquant à deviner les emails existants.
    $_SESSION['error'] = 'Email ou mot de passe incorrect.';
    header('Location: login.php');
    exit;
}

// ============================================================
// ÉTAPE 6: CRÉER LA SESSION UTILISATEUR
// ============================================================
// On stocke les informations essentielles de l'utilisateur dans la session.
// La session est comme un "badge" que le serveur donne à l'utilisateur
// pour qu'il n'ait pas à se reconnecter à chaque page.
$_SESSION['user'] = [
    'id'    => $user['id'],     // Identifiant unique en base de données
    'nom'   => $user['nom'],    // Nom complet (affiché dans la navbar)
    'email' => $user['email'],  // Adresse email
    'role'  => $user['role'],   // Rôle: 'admin', 'manager', 'employe', ou 'client'
];

// ============================================================
// ÉTAPE 7: REDIRECTION VERS LE TABLEAU DE BORD
// ============================================================
// Après connexion réussie, on redirige vers index.php
// qui se chargera de rediriger vers le bon tableau de bord selon le rôle
header('Location: ../index.php');
exit;