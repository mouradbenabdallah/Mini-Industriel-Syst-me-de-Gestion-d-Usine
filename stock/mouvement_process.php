<?php
session_start();
$allowed_roles = ['admin','manager','employe'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mouvement.php');
    exit;
}

$produit_id = (int)($_POST['produit_id'] ?? 0);
$type       = $_POST['type'] ?? '';
$quantite   = (float)($_POST['quantite'] ?? 0);
$note       = trim($_POST['note'] ?? '');
$user_id    = $_SESSION['user']['id'];

if (!$produit_id || !in_array($type, ['entree','sortie']) || $quantite <= 0) {
    $_SESSION['error'] = 'Données invalides.';
    header('Location: mouvement.php');
    exit;
}



try {
    $pdo->beginTransaction();

    // Lock the product row
    $stmt = $pdo->prepare('SELECT quantite FROM produits WHERE id=? FOR UPDATE');
    $stmt->execute([$produit_id]);
    $produit = $stmt->fetch();

    if (!$produit) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Produit introuvable.';
        header('Location: mouvement.php');
        exit;
    }

    // Guard: cannot remove more than available
    if ($type === 'sortie' && $produit['quantite'] < $quantite) {
        $pdo->rollBack();
        $_SESSION['error'] = sprintf(
            'Stock insuffisant. Disponible: %s �TND� Demandé: %s',
            $produit['quantite'],
            $quantite
        );
        header('Location: mouvement.php?produit_id=' . $produit_id);
        exit;
    }

    // INSERT movement
    $stmt = $pdo->prepare(
        'INSERT INTO mouvements (produit_id, type, quantite, note, user_id) VALUES (?,?,?,?,?)'
    );
    $stmt->execute([$produit_id, $type, $quantite, $note ?: null, $user_id]);

    // UPDATE product quantity
    if ($type === 'entree') {
        $stmt = $pdo->prepare('UPDATE produits SET quantite = quantite + ? WHERE id=?');
    } else {
        $stmt = $pdo->prepare('UPDATE produits SET quantite = quantite - ? WHERE id=?');
    }
    $stmt->execute([$quantite, $produit_id]);

    $pdo->commit();

    $_SESSION['success'] = sprintf(
        'Mouvement de %s (%s %s) enregistré avec succès.',
        $type === 'entree' ? 'entrée' : 'sortie',
        $quantite,
        $type === 'entree' ? 'ajouté' : 'retiré'
    );
    header('Location: view.php?id=' . $produit_id);
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Erreur lors du mouvement: ' . $e->getMessage();
    header('Location: mouvement.php?produit_id=' . $produit_id);
    exit;
}
