<?php
session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}


$stmt = $pdo->prepare('SELECT id FROM produits WHERE id=?');
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    $_SESSION['error'] = 'Produit introuvable.';
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM produits WHERE id=?');
$stmt->execute([$id]);

$_SESSION['success'] = 'Produit supprimé.';
header('Location: index.php');
exit;
