<?php
/**
 * Vérification d'authentification
 * Ce fichier doit être inclus dans toutes les pages protégées
 * 
 * Fonctionnalités:
 * - Vérifie que l'utilisateur est connecté
 * - Vérifie que l'utilisateur a le bon rôle (si spécifié)
 * - Définit les fonctions helper pour vérifier les rôles
 * 
 * Usage:
 *   $allowed_roles = ['admin', 'manager'];
 *   require_once '../includes/auth_check.php';
 * 
 * Projet: Usine Industriel - Système de gestion d'usine
 * Mode: Procédural (sans POO)
 */

// Définition de BASE_URL si pas encore défini (peut être écrasé par header.php)
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $base = preg_replace('#/usine_industriel/.*#', '/usine_industriel/', $script);
    define('BASE_URL', $protocol . '://' . $host . $base);
}

// Niveau 1: Vérification que l'utilisateur est connecté
// Si pas de session utilisateur, redirection vers la page de connexion
if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit;
}

// Niveau 2: Vérification du rôle (si la variable $allowed_roles est définie par la page)
// Si le rôle de l'utilisateur n'est pas dans la liste des rôles autorisés, redirection vers l'accueil
if (isset($allowed_roles) && !in_array($_SESSION['user']['role'], $allowed_roles)) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

/**
 * Fonction helper: vérifie si l'utilisateur a un rôle spécifique
 * 
 * @param string $role Le rôle à vérifier
 * @return bool True si l'utilisateur a ce rôle, false sinon
 */
function hasRole(string $role): bool {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
}

/**
 * Fonction helper: vérifie si l'utilisateur a un des rôles spécifiés
 * 
 * @param array $roles Tableau des rôles à vérifier
 * @return bool True si l'utilisateur a un des rôles, false sinon
 */
function hasAnyRole(array $roles): bool {
    return isset($_SESSION['user']['role']) && in_array($_SESSION['user']['role'], $roles);
}