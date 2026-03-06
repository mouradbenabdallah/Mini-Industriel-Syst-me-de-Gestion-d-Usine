<?php
// Traitement du changement de statut

session_start();
$allowed_roles = ['admin','manager','employe'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$next_status = $_POST['next_status'] ?? '';
$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];

$valid_transitions = [
    'en_attente' => 'en_cours',
    'en_cours' => 'termine',
];

if (!$id || !in_array($next_status, array_values($valid_transitions))) {
    $_SESSION['error'] = 'Données invalides.';
    header('Location: index.php');
    exit;
}

// Récupérer l'ordre
$stmt = $pdo->prepare('SELECT * FROM ordres WHERE id=?');
$stmt->execute([$id]);
$ordre = $stmt->fetch();

if (!$ordre) {
    $_SESSION['error'] = 'Ordre introuvable.';
    header('Location: index.php');
    exit;
}

// Vérifier la transition
if (($valid_transitions[$ordre['statut']] ?? '') !== $next_status) {
    $_SESSION['error'] = 'Transition invalide.';
    header('Location: view.php?id=' . $id);
    exit;
}

// Vérifier si l'employé peut modifier cet ordre
if ($role === 'employe') {
    if (!$ordre['employe_id']) {
        $_SESSION['error'] = 'Vous ne pouvez pas avancer cet ordre.';
        header('Location: view.php?id=' . $id);
        exit;
    }
    $stmt = $pdo->prepare('SELECT user_id FROM employes WHERE id = ?');
    $stmt->execute([$ordre['employe_id']]);
    $emp = $stmt->fetch();
    if (!$emp || $emp['user_id'] !== $user_id) {
        $_SESSION['error'] = 'Vous ne pouvez avancer que vos propres ordres.';
        header('Location: view.php?id=' . $id);
        exit;
    }
}

// Mettre à jour le statut
$stmt = $pdo->prepare('UPDATE ordres SET statut=? WHERE id=?');
$stmt->execute([$next_status, $id]);

$_SESSION['success'] = 'Statut mis à jour : ' . $next_status . '.';
header('Location: view.php?id=' . $id);
exit;