<?php
session_start();
$allowed_roles = ['admin'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit;
}

$id       = (int)($_POST['id'] ?? 0);
$nom      = trim($_POST['nom'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'client';
$actif    = isset($_POST['actif']) ? 1 : 0;

$valid_roles = ['admin','manager','employe','client'];
if (!$id || !$nom || !$email || !in_array($role, $valid_roles)) {
    $_SESSION['error'] = 'Données invalides.';
    header('Location: user_edit.php?id=' . $id);
    exit;
}



// Check email uniqueness (excluding current user)
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
$stmt->execute([$email, $id]);
if ($stmt->fetch()) {
    $_SESSION['error'] = 'Cet email est déjà utilisé.';
    header('Location: user_edit.php?id=' . $id);
    exit;
}

if ($password) {
    $stmt = $pdo->prepare('UPDATE users SET nom=?, email=?, password=?, role=?, actif=? WHERE id=?');
    $stmt->execute([$nom, $email, $password, $role, $actif, $id]);
} else {
    $stmt = $pdo->prepare('UPDATE users SET nom=?, email=?, role=?, actif=? WHERE id=?');
    $stmt->execute([$nom, $email, $role, $actif, $id]);
}

$_SESSION['success'] = 'Utilisateur mis à jour.';
header('Location: users.php');
exit;
