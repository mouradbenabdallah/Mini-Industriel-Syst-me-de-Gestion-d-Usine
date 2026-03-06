<?php
session_start();
$allowed_roles = ['admin'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: user_add.php');
    exit;
}

$nom      = trim($_POST['nom'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'client';
$actif    = isset($_POST['actif']) ? 1 : 0;

$valid_roles = ['admin', 'manager', 'employe', 'client'];
if (!$nom || !$email || !$password || !in_array($role, $valid_roles)) {
    $_SESSION['error'] = 'Données invalides.';
    header('Location: user_add.php');
    exit;
}



$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $_SESSION['error'] = 'Cet email est déjà utilisé.';
    header('Location: user_add.php');
    exit;
}

$stmt = $pdo->prepare('INSERT INTO users (nom, email, password, role, actif) VALUES (?,?,?,?,?)');
$stmt->execute([$nom, $email, $password, $role, $actif]);

$_SESSION['success'] = 'Utilisateur créé avec succès.';
header('Location: users.php');
exit;
