<?php
session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: employes.php');
    exit;
}


$stmt = $pdo->prepare('SELECT user_id FROM employes WHERE id=?');
$stmt->execute([$id]);
$employe = $stmt->fetch();

if (!$employe) {
    $_SESSION['error'] = 'Employé introuvable.';
    header('Location: employes.php');
    exit;
}

// Soft-delete via users.actif = 0
$stmt = $pdo->prepare('UPDATE users SET actif=0 WHERE id=?');
$stmt->execute([$employe['user_id']]);

$_SESSION['success'] = 'Employé désactivé.';
header('Location: employes.php');
exit;
