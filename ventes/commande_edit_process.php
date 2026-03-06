<?php
session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: commandes.php');
    exit;
}

$id     = (int)($_POST['id'] ?? 0);
$statut = $_POST['statut'] ?? '';

$valid_statuts = ['en_attente','confirmee','livree','annulee'];
if (!$id || !in_array($statut, $valid_statuts)) {
    $_SESSION['error'] = 'Données invalides.';
    header('Location: commande_edit.php?id=' . $id);
    exit;
}


$stmt = $pdo->prepare('UPDATE commandes SET statut=? WHERE id=?');
$stmt->execute([$statut, $id]);

$_SESSION['success'] = 'Statut mis à jour : ' . $statut . '.';
header('Location: commande_view.php?id=' . $id);
exit;
