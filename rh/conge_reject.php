<?php
session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: conges.php');
    exit;
}


$stmt = $pdo->prepare("SELECT id FROM conges WHERE id=? AND statut='en_attente'");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    $_SESSION['error'] = 'Demande introuvable ou déjà traitée.';
    header('Location: conges.php');
    exit;
}

$stmt = $pdo->prepare("UPDATE conges SET statut='rejete' WHERE id=? AND statut='en_attente'");
$stmt->execute([$id]);

$_SESSION['success'] = 'Congé rejeté.';
header('Location: conges.php');
exit;
