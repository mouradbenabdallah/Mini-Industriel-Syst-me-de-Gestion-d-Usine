<?php
session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: commandes.php');
    exit;
}


$stmt = $pdo->prepare('SELECT id FROM commandes WHERE id=?');
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    $_SESSION['error'] = 'Commande introuvable.';
    header('Location: commandes.php');
    exit;
}

$stmt = $pdo->prepare("UPDATE commandes SET statut='annulee' WHERE id=?");
$stmt->execute([$id]);

$_SESSION['success'] = 'Commande annulée.';
header('Location: commandes.php');
exit;
