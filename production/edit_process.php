<?php
/**
 * Traitement de la modification d'un ordre de production
 * Met à jour les informations d'un ordre existant
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
$id          = (int)($_POST['id'] ?? 0);                    // ID de l'ordre à modifier
$titre       = trim($_POST['titre'] ?? '');                 // Nouveau titre
$description = trim($_POST['description'] ?? '') ?: null;   // Nouvelle description
$statut      = $_POST['statut'] ?? '';                      // Nouveau statut
$employe_id  = (int)($_POST['employe_id'] ?? 0) ?: null;    // Nouvel employé assigné

// Liste des statuts valides
$valid_statuts = ['en_attente','en_cours','termine'];

// Validation des données
if (!$id || !$titre || !in_array($statut, $valid_statuts)) {
    $_SESSION['error'] = 'Données invalides.';
    header('Location: edit.php?id=' . $id);
    exit;
}

// Mise à jour de l'ordre dans la base de données
$stmt = $pdo->prepare(
    'UPDATE ordres SET titre=?, description=?, statut=?, employe_id=? WHERE id=?'
);
$stmt->execute([$titre, $description, $statut, $employe_id, $id]);

// Message de succès et redirection vers la page de vue
$_SESSION['success'] = 'Ordre mis à jour.';
header('Location: view.php?id=' . $id);
exit;