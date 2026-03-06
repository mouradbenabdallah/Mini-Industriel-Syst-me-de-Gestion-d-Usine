<?php
session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: employes.php');
    exit;
}

$id           = (int)($_POST['id'] ?? 0);
$poste        = trim($_POST['poste'] ?? '');
$salaire_base = (float)($_POST['salaire_base'] ?? 0);
$telephone    = trim($_POST['telephone'] ?? '') ?: null;
$date_embauche = $_POST['date_embauche'] ?? '';

if (!$id || !$poste) {
    $_SESSION['error'] = 'Données invalides.';
    header('Location: employe_edit.php?id=' . $id);
    exit;
}


$stmt = $pdo->prepare(
    'UPDATE employes SET poste=?, salaire_base=?, telephone=?, date_embauche=? WHERE id=?'
);
$stmt->execute([$poste, $salaire_base, $telephone, $date_embauche ?: null, $id]);

$_SESSION['success'] = 'Employé mis à jour.';
header('Location: employes.php');
exit;
