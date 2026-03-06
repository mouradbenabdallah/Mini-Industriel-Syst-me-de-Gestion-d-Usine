<?php
session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: salaire_add.php');
    exit;
}

$employe_id   = (int)($_POST['employe_id'] ?? 0);
$mois         = trim($_POST['mois'] ?? '');
$montant      = (float)($_POST['montant'] ?? 0);
$date_paiement = $_POST['date_paiement'] ?? '';

if (!$employe_id || !$mois || !$date_paiement || $montant < 0) {
    $_SESSION['error'] = 'Données invalides.';
    header('Location: salaire_add.php');
    exit;
}

if (!preg_match('/^\d{4}-\d{2}$/', $mois)) {
    $_SESSION['error'] = 'Format du mois invalide.';
    header('Location: salaire_add.php');
    exit;
}



try {
    $stmt = $pdo->prepare(
        'INSERT INTO salaires (employe_id, mois, montant, date_paiement) VALUES (?,?,?,?)'
    );
    $stmt->execute([$employe_id, $mois, $montant, $date_paiement]);
    $_SESSION['success'] = 'Paiement enregistré.';
    header('Location: salaires.php');
    exit;
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        $_SESSION['error'] = 'Un salaire a déjà été enregistré pour cet employé ce mois-ci.';
    } else {
        $_SESSION['error'] = 'Erreur: ' . $e->getMessage();
    }
    header('Location: salaire_add.php');
    exit;
}
