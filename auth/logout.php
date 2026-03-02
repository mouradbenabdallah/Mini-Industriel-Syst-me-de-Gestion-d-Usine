<?php
/**
 * Déconnexion utilisateur
 * Détruit la session et redirige vers la page de connexion
 * 
 * Projet: Usine Industriel - Système de gestion d'usine
 * Mode: Procédural (sans POO)
 */

// Démarrage de la session (nécessaire pour pouvoir la détruire)
session_start();

// Destruction complète de la session
// Cela supprime toutes les données de session stockées côté serveur
session_destroy();

// Redirection vers la page de connexion
header('Location: login.php');
exit;