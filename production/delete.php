<?php
// Supprimer un ordre de production

session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: index.php');
    exit;
}

// Vérifier que l'ordre existe
$stmt = $pdo->prepare('SELECT id FROM ordres WHERE id=?');
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    $_SESSION['error'] = 'Ordre introuvable.';
    header('Location: index.php');
    exit;
}

// Supprimer l'ordre
$stmt = $pdo->prepare('DELETE FROM ordres WHERE id=?');
$stmt->execute([$id]);

$_SESSION['success'] = 'Ordre supprimé.';
header('Location: index.php');
exit;