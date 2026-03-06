<?php
session_start();
$allowed_roles = ['admin','manager','employe'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';


$produits = $pdo->query('SELECT id, nom, quantite FROM produits ORDER BY nom')->fetchAll();

$selected_produit_id = (int)($_GET['produit_id'] ?? 0);

$page_title   = 'Mouvement de stock';
$module_color = 'warning';
require_once '../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-arrow-left-right"></i> Mouvement de stock</h2>
    <a href="index.php" class="btn btn-outline-warning"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="mouvement_process.php">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Produit</label>
                    <select name="produit_id" class="form-select" required>
                        <option value="">‚TNDî Choisir un produit ‚TNDî</option>
                        <?php foreach ($produits as $p): ?>
                        <option value="<?= $p['id'] ?>"
                            <?= $p['id'] === $selected_produit_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nom']) ?> (stock: <?= $p['quantite'] ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select" required>
                        <option value="entree">‚Üë Entr√©e</option>
                        <option value="sortie">‚Üì Sortie</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quantit√©</label>
                    <input type="number" name="quantite" class="form-control" min="0.01" step="0.01" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Note <small class="text-muted">(optionnel)</small></label>
                    <textarea name="note" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
