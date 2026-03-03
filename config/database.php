<?php
/**
 * Fichier de configuration de la base de données
 * Ce fichier établit la connexion à la base de données MySQL
 * 
 * Mode: Procédural (sans POO)
 */

// Configuration de la connexion à la base de données
$host = 'localhost';          // Adresse du serveur MySQL (localhost = serveur local)
$dbname = 'usine_industriel'; // Nom de la base de données
$username = 'root';           // Nom d'utilisateur MySQL (root par défaut sur XAMPP)
$password = '';               // Mot de passe MySQL (vide par défaut sur XAMPP)
$charset = 'utf8mb4';          // Encodage des caractères (utf8mb4 pour supporter les emojis et caractères spéciaux)

// Construction du Data Source Name (DSN) pour la connexion PDO
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// Options de configuration PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Activer les exceptions en cas d'erreur
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Retourner les résultats en tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                      // Désactiver l'émulation des requêtes préparées (plus sécurisé)
];

// Tentative de connexion à la base de données
try {
    // Création de l'objet PDO pour la connexion
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // En cas d'erreur de connexion, afficher un message et arrêter le script
    die('Erreur de connexion à la base de données: ' . $e->getMessage());
}

// Exportation de la variable $pdo pour pouvoir l'utiliser dans d'autres fichiers
// Pour l'utiliser dans un autre fichier: require_once 'config/database.php';
// Ensuite vous pouvez utiliser $pdo pour exécuter des requêtes