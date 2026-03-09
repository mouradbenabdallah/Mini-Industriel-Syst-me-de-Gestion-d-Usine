<?php
// Modifier le statut d'une commande

session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$id = (int)($_GET['id'] ?? 0);

// Récupérer la commande
$stmt = $pdo->prepare('SELECT * FROM commandes WHERE id = ?');
$stmt->execute([$id]);
$commande = $stmt->fetch();

if (!$commande) {
    $_SESSION['error'] = 'Commande introuvable.';
    header('Location: commandes.php');
    exit;
}

// Récupérer le nom du client séparément
$stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
$stmt->execute([$commande['client_id']]);
$client = $stmt->fetch();

// Récupérer le nom du produit séparément
$stmt = $pdo->prepare('SELECT nom FROM produits WHERE id = ?');
$stmt->execute([$commande['produit_id']]);
$produit = $stmt->fetch();

$page_title = 'Modifier commande #' . $commande['id'];
$module_color = 'danger';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Modifier commande #<?= $commande['id'] ?></h2>
    <a href="commande_view.php?id=<?= $commande['id'] ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="mb-3">
            <strong>Client:</strong> <?= htmlspecialchars($client['nom'] ?? '') ?> |
            <strong>Produit:</strong> <?= htmlspecialchars($produit['nom'] ?? '') ?> |
            <strong>Total:</strong> <?= number_format($commande['total'], 2, ',', ' ') ?> TND
        </div>
        <form method="post" action="commande_edit_process.php">
            <input type="hidden" name="id" value="<?= $commande['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-select" required>
                    <option value="en_attente" <?= $commande['statut']==='en_attente'?'selected':'' ?>>En attente
                    </option>
                    <option value="confirmee" <?= $commande['statut']==='confirmee'?'selected':'' ?>>Confirmée</option>
                    <option value="livree" <?= $commande['statut']==='livree'?'selected':'' ?>>Livrée</option>
                    <option value="annulee" <?= $commande['statut']==='annulee'?'selected':'' ?>>Annulée</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Enregistrer</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>