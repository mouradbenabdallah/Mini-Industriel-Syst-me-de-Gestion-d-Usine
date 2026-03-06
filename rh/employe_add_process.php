<?php
session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: employe_add.php');
    exit;
}

$user_id      = (int)($_POST['user_id'] ?? 0);
$poste        = trim($_POST['poste'] ?? '');
$salaire_base = (float)($_POST['salaire_base'] ?? 0);
$telephone    = trim($_POST['telephone'] ?? '') ?: null;
$date_embauche = $_POST['date_embauche'] ?? '';

if (!$user_id || !$poste) {
    $_SESSION['error'] = 'Données invalides.';
    header('Location: employe_add.php');
    exit;
}



// Verify user exists with role=employe
$stmt = $pdo->prepare("SELECT id FROM users WHERE id=? AND role='employe' AND actif=1");
$stmt->execute([$user_id]);
if (!$stmt->fetch()) {
    $_SESSION['error'] = 'Utilisateur invalide.';
    header('Location: employe_add.php');
    exit;
}

// Check not already in employes
$stmt = $pdo->prepare('SELECT id FROM employes WHERE user_id=?');
$stmt->execute([$user_id]);
if ($stmt->fetch()) {
    $_SESSION['error'] = 'Cet utilisateur est déjà enregistré comme employé.';
    header('Location: employe_add.php');
    exit;
}

$stmt = $pdo->prepare(
    'INSERT INTO employes (user_id, poste, salaire_base, telephone, date_embauche) VALUES (?,?,?,?,?)'
);
$stmt->execute([
    $user_id,
    $poste,
    $salaire_base,
    $telephone,
    $date_embauche ?: null
]);

$_SESSION['success'] = 'Employé ajouté avec succès.';
header('Location: employes.php');
exit;
