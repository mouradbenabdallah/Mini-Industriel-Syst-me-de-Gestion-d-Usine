<?php
/**
 * Configuration de la base de données
 * Ce fichier établit la connexion à MySQL avec PDO
 */

// ===== PARAMÈTRES DE CONNEXION =====
$host = "localhost"; // Adresse du serveur MySQL
$dbname = "usine_industriel"; // Nom de la base de données
$username = "root"; // Utilisateur MySQL (root par défaut sur XAMPP)
$password = ""; // Mot de passe (vide par défaut sur XAMPP)
$charset = "utf8mb4"; // Encodage (pour accents et emojis)

// ===== CONSTRUCTION DU DSN =====
// DSN = Data Source Name (chaîne de connexion)
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// ===== OPTIONS PDO =====
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Afficher les erreurs SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Résultats en tableau associatif
    PDO::ATTR_EMULATE_PREPARES => false, // Vraies requêtes préparées (sécurité)
];

// ===== CONNEXION =====
try {
    // Créer la connexion PDO
    $pdo = new PDO($dsn, $username, $password, $options);
    // ✅ Connexion réussie ! $pdo est maintenant utilisable
} catch (PDOException $e) {
    // ❌ Erreur de connexion
    die("Erreur de connexion: " . $e->getMessage());
}

// Maintenant vous pouvez utiliser $pdo pour faire des requêtes SQL
// Exemple: $stmt = $pdo->query('SELECT * FROM produits');
