<?php
session_start();
$allowed_roles = ['employe'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: conge_add.php');
    exit;
}

$date_debut = $_POST['date_debut'] ?? '';
$date_fin   = $_POST['date_fin'] ?? '';
$motif      = trim($_POST['motif'] ?? '') ?: null;
$user_id    = $_SESSION['user']['id'];

if (!$date_debut || !$date_fin || $date_fin < $date_debut) {
    $_SESSION['error'] = 'Dates invalides.';
    header('Location: conge_add.php');
    exit;
}



$stmt = $pdo->prepare('SELECT id FROM employes WHERE user_id=?');
$stmt->execute([$user_id]);
$employe = $stmt->fetch();

if (!$employe) {
    $_SESSION['error'] = 'Profil employé introuvable. Contactez un manager.';
    header('Location: conge_add.php');
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO conges (employe_id, date_debut, date_fin, motif, statut) VALUES (?,?,?,?,'en_attente')"
);
$stmt->execute([$employe['id'], $date_debut, $date_fin, $motif]);

$_SESSION['success'] = 'Demande de congé soumise avec succès.';
header('Location: conges.php');
exit;
