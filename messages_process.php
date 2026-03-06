<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$destinataire_id = (int)($_POST['destinataire_id'] ?? 0);
$contenu         = trim($_POST['contenu'] ?? '');
$redirect        = $_POST['redirect'] ?? 'index.php';

// Sanitize redirect to prevent open redirect
$redirect = ltrim($redirect, '/');
if (preg_match('#^https?://#', $redirect)) {
    $redirect = 'index.php';
}

if (!$destinataire_id || !$contenu) {
    $_SESSION['error'] = 'Destinataire et message obligatoires.';
    header('Location: ' . $redirect);
    exit;
}



// Verify recipient exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE id=? AND actif=1');
$stmt->execute([$destinataire_id]);
if (!$stmt->fetch()) {
    $_SESSION['error'] = 'Destinataire introuvable.';
    header('Location: ' . $redirect);
    exit;
}

$stmt = $pdo->prepare(
    'INSERT INTO messages (expediteur_id, destinataire_id, contenu) VALUES (?,?,?)'
);
$stmt->execute([$_SESSION['user']['id'], $destinataire_id, $contenu]);

$_SESSION['success'] = 'Message envoyé.';
header('Location: ' . $redirect);
exit;
