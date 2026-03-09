<?php
// Voir les détails d'un produit en stock

session_start();
$allowed_roles = ['admin','manager','employe'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$id = (int)($_GET['id'] ?? 0);

// Récupérer le produit
$stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
    $_SESSION['error'] = 'Produit introuvable.';
    header('Location: index.php');
    exit;
}

// Récupérer les mouvements séparément (sans JOIN)
$stmt = $pdo->prepare('SELECT * FROM mouvements WHERE produit_id = ? ORDER BY created_at DESC LIMIT 50');
$stmt->execute([$id]);
$mouvements = $stmt->fetchAll();

// Pour chaque mouvement, récupérer le nom de l'utilisateur
foreach ($mouvements as &$m) {
    $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
    $stmt->execute([$m['user_id']]);
    $user = $stmt->fetch();
    $m['user_nom'] = $user['nom'] ?? 'Inconnu';
}

$page_title = 'Produit : ' . $produit['nom'];
$module_color = 'warning';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam"></i> <?= htmlspecialchars($produit['nom']) ?></h2>
    <div>
        <a href="mouvement.php?produit_id=<?= $produit['id'] ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-right"></i> Mouvement
        </a>
        <?php if (hasAnyRole(['admin','manager'])): ?>
        <a href="edit.php?id=<?= $produit['id'] ?>" class="btn btn-outline-primary ms-1">
            <i class="bi bi-pencil"></i> Modifier
        </a>
        <?php endif; ?>
        <a href="index.php" class="btn btn-outline-secondary ms-1"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div
            class="card text-center <?= $produit['quantite'] <= $produit['quantite_min'] ? 'border-danger' : 'border-success' ?>">
            <div class="card-body">
                <h3 class="<?= $produit['quantite'] <= $produit['quantite_min'] ? 'text-danger' : 'text-success' ?>">
                    <?= $produit['quantite'] ?>
                </h3>
                <p class="text-muted mb-0">Quantité actuelle</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3><?= $produit['quantite_min'] ?></h3>
                <p class="text-muted mb-0">Quantité min. (alerte)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3><?= number_format($produit['prix'], 2, ',', ' ') ?> TND</h3>
                <p class="text-muted mb-0">Prix unitaire</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3><?= $produit['categorie'] === 'matiere_premiere' ? 'Matière' : 'Fini' ?></h3>
                <p class="text-muted mb-0">Catégorie</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-clock-history"></i> Historique des mouvements (50 derniers)</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>Note</th>
                    <th>Utilisateur</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mouvements as $m): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                    <td>
                        <span class="badge bg-<?= $m['type']==='entree' ? 'success' : 'danger' ?>">
                            <?= $m['type'] === 'entree' ? '↑ Entrée' : '↓ Sortie' ?>
                        </span>
                    </td>
                    <td><?= $m['quantite'] ?></td>
                    <td><?= htmlspecialchars($m['note'] ?? '') ?></td>
                    <td><?= htmlspecialchars($m['user_nom']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$mouvements): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">Aucun mouvement enregistré.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>