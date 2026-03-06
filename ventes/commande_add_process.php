<?php
session_start();
$allowed_roles = ['client','admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: commande_add.php');
    exit;
}

$produit_id = (int)($_POST['produit_id'] ?? 0);
$quantite   = (float)($_POST['quantite'] ?? 0);
$client_id  = $_SESSION['user']['id'];

if (!$produit_id || $quantite <= 0) {
    $_SESSION['error'] = 'Données invalides.';
    header('Location: commande_add.php');
    exit;
}



$stmt = $pdo->prepare('SELECT * FROM produits WHERE id=?');
$stmt->execute([$produit_id]);
$produit = $stmt->fetch();

if (!$produit) {
    $_SESSION['error'] = 'Produit introuvable.';
    header('Location: commande_add.php');
    exit;
}

// Stock check
if ($produit['quantite'] < $quantite) {
    $_SESSION['error'] = sprintf(
        'Stock insuffisant. Disponible: %s, Demandé: %s',
        $produit['quantite'],
        $quantite
    );
    header('Location: commande_add.php');
    exit;
}

// Total always computed server-side
$total = $quantite * $produit['prix'];

$stmt = $pdo->prepare(
    "INSERT INTO commandes (client_id, produit_id, quantite, statut, total) VALUES (?,?,?,'en_attente',?)"
);
$stmt->execute([$client_id, $produit_id, $quantite, $total]);
$id = $pdo->lastInsertId();

$_SESSION['success'] = 'Commande passée avec succès. Total: ' . number_format($total, 2, ',', ' ') . ' TND';
header('Location: commande_view.php?id=' . $id);
exit;