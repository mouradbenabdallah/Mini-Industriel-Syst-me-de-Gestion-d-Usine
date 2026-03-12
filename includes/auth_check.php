<?php
/**
 * Vérification d'authentification
 * Protège les pages - vérifie que l'utilisateur est connecté et a le bon rôle
 */

// ===== DÉFINIR L'URL DE BASE =====
// Exemple: http://localhost/usine_industriel/
if (!defined("BASE_URL")) {
    $protocol =
        !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off"
            ? "https"
            : "http";
    $host = $_SERVER["HTTP_HOST"] ?? "localhost";
    $script = $_SERVER["SCRIPT_NAME"] ?? "";
    $base = preg_replace(
        "#/usine_industriel/.*#",
        "/usine_industriel/",
        $script,
    );
    define("BASE_URL", $protocol . "://" . $host . $base);
}

// ===== VÉRIFICATION 1 : Utilisateur connecté ? =====
// Si $_SESSION['user'] n'existe pas → pas connecté → redirection login
if (!isset($_SESSION["user"])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit(); // Toujours mettre exit() après une redirection
}

// ===== VÉRIFICATION 2 : Bon rôle ? (optionnel) =====
// Si $allowed_roles est défini dans la page, on vérifie le rôle
// Exemple: $allowed_roles = ['admin', 'manager'];
if (isset($allowed_roles)) {
    // Si le rôle de l'utilisateur n'est pas dans $allowed_roles → redirection accueil
    if (!in_array($_SESSION["user"]["role"], $allowed_roles)) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
}

// ===== FONCTIONS HELPER =====

/**
 * Vérifie si l'utilisateur a un rôle spécifique
 * Usage: if (hasRole('admin')) { ... }
 */
function hasRole(string $role): bool
{
    return isset($_SESSION["user"]["role"]) &&
        $_SESSION["user"]["role"] === $role;
}

/**
 * Vérifie si l'utilisateur a l'un des rôles
 * Usage: if (hasAnyRole(['admin', 'manager'])) { ... }
 */
function hasAnyRole(array $roles): bool
{
    return isset($_SESSION["user"]["role"]) &&
        in_array($_SESSION["user"]["role"], $roles);
}

// Maintenant la page est protégée et les fonctions hasRole() et hasAnyRole() sont disponibles
