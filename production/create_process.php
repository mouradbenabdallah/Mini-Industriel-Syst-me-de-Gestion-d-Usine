<?php
/**
 * Traitement de la création d'un ordre de production
 * Insère un nouvel ordre dans la base de données
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
$titre       = trim($_POST['titre'] ?? '');                  // Titre de l'ordre
$description = trim($_POST['description'] ?? '') ?: null;    // Description (peut être null)
$employe_id  = (int)($_POST['employe_id'] ?? 0) ?: null;    // ID de l'employé assigné
$manager_id  = $_SESSION['user']['id'];                     // ID du manager qui crée l'ordre

// Validation: le titre est obligatoire
if (!$titre) {
    $_SESSION['error'] = 'Le titre est obligatoire.';
    header('Location: create.php');
    exit;
}

// Insertion du nouvel ordre de production dans la base de données
// Statut par défaut: 'en_attente'
$stmt = $pdo->prepare(
    "INSERT INTO ordres (titre, description, statut, employe_id, manager_id) VALUES (?,?,'en_attente',?,?)"
);
$stmt->execute([$titre, $description, $employe_id, $manager_id]);

// Récupération de l'ID de l'ordre nouvellement créé
$id = $pdo->lastInsertId();

// Message de succès et redirection vers la page de vue de l'ordre
$_SESSION['success'] = 'Ordre de production créé.';
header('Location: view.php?id=' . $id);
exit;