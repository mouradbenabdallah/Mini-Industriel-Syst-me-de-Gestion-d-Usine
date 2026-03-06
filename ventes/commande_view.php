<?php
// Détail d'une commande

session_start();
$allowed_roles = ['admin','manager','client'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];
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

// Vérifier les permissions
if ($role === 'client' && $commande['client_id'] != $user_id) {
    $_SESSION['error'] = 'Accès refusé.';
    header('Location: commandes.php');
    exit;
}

// Récupérer le nom du client séparément
$stmt = $pdo->prepare('SELECT nom, email FROM users WHERE id = ?');
$stmt->execute([$commande['client_id']]);
$client = $stmt->fetch();

// Récupérer le nom du produit séparément
$stmt = $pdo->prepare('SELECT nom, prix, categorie FROM produits WHERE id = ?');
$stmt->execute([$commande['produit_id']]);
$produit = $stmt->fetch();

$page_title = 'Commande #' . $id;
$module_color = 'danger';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cart"></i> Commande #<?= $id ?></h2>
    <a href="commandes.php" class="btn btn-outline-danger"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Détails de la commande</strong>
                <span class="badge badge-<?= $commande['statut'] ?>"><?= $commande['statut'] ?></span>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5">Client</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($client['nom'] ?? '') ?></dd>
                    <dt class="col-sm-5">Email</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($client['email'] ?? '') ?></dd>
                    <dt class="col-sm-5">Produit</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($produit['nom'] ?? '') ?></dd>
                    <dt class="col-sm-5">Prix unitaire</dt>
                    <dd class="col-sm-7"><?= number_format($produit['prix'] ?? 0, 2, ',', ' ') ?> TND</dd>
                    <dt class="col-sm-5">Quantité</dt>
                    <dd class="col-sm-7"><?= $commande['quantite'] ?></dd>
                    <dt class="col-sm-5">Total</dt>
                    <dd class="col-sm-7 fw-bold fs-5 text-danger"><?= number_format($commande['total'], 2, ',', ' ') ?>
                        TND</dd>
                    <dt class="col-sm-5">Date</dt>
                    <dd class="col-sm-7"><?= date('d/m/Y H:i', strtotime($commande['created_at'])) ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <?php if (in_array($role, ['admin','manager'])): ?>
        <div class="card">
            <div class="card-header">Modifier le statut</div>
            <div class="card-body">
                <form method="post" action="commande_edit_process.php">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <select name="statut" class="form-select mb-2">
                        <option value="en_attente" <?= $commande['statut']==='en_attente'?'selected':'' ?>>En attente
                        </option>
                        <option value="en_cours" <?= $commande['statut']==='en_cours'?'selected':'' ?>>En cours</option>
                        <option value="livree" <?= $commande['statut']==='livree'?'selected':'' ?>>Livrée</option>
                        <option value="annulee" <?= $commande['statut']==='annulee'?'selected':'' ?>>Annulée</option>
                    </select>
                    <button type="submit" class="btn btn-danger w-100">Mettre à jour</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="card mt-3">
            <div class="card-body">
                <a href="facture.php?id=<?= $id ?>" class="btn btn-outline-dark w-100">
                    <i class="bi bi-file-pdf"></i> Télécharger facture
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>