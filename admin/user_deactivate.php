<?php
session_start();
$allowed_roles = ['admin'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id || $id === $_SESSION['user']['id']) {
    $_SESSION['error'] = 'Action non autorisée.';
    header('Location: users.php');
    exit;
}


$stmt = $pdo->prepare('SELECT actif FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = 'Utilisateur introuvable.';
    header('Location: users.php');
    exit;
}

$new_actif = $user['actif'] ? 0 : 1;
$stmt = $pdo->prepare('UPDATE users SET actif = ? WHERE id = ?');
$stmt->execute([$new_actif, $id]);

$_SESSION['success'] = 'Statut mis à jour.';
header('Location: users.php');
exit;
