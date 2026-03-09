<?php
// Ajouter une nouvelle commande

session_start();
$allowed_roles = ['admin','manager','client'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Récupérer les produits disponibles
$stmt = $pdo->query("SELECT * FROM produits WHERE quantite > 0 ORDER BY nom");
$produits = $stmt->fetchAll();

$page_title = 'Nouvelle commande';
$module_color = 'danger';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cart-plus"></i> Nouvelle commande</h2>
    <a href="commandes.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="commande_add_process.php">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Produit</label>
                    <select name="produit_id" class="form-select" required onchange="updatePrix()">
                        <option value="">— Choisir —</option>
                        <?php foreach ($produits as $p): ?>
                        <option value="<?= $p['id'] ?>" data-prix="<?= $p['prix'] ?>"
                            data-stock="<?= $p['quantite'] ?>">
                            <?= htmlspecialchars($p['nom']) ?> - <?= number_format($p['prix'], 2, ',', ' ') ?> TND
                            (stock: <?= $p['quantite'] ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quantité</label>
                    <input type="number" name="quantite" class="form-control" min="1" value="1" required
                        onchange="updateTotal()">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Prix unitaire</label>
                    <div class="input-group">
                        <input type="text" id="prix_unit" class="form-control" disabled value="—">
                        <span class="input-group-text">TND</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Total estimé</label>
                    <div class="input-group">
                        <input type="text" id="total_est" class="form-control fw-bold" disabled value="—">
                        <span class="input-group-text">TND</span>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Passer la commande</button>
            </div>
        </form>
    </div>
</div>

<script>
function updatePrix() {
    var select = document.querySelector('select[name="produit_id"]');
    var prix = select.options[select.selectedIndex].getAttribute('data-prix');
    if (prix) {
        document.getElementById('prix_unit').value = prix;
        updateTotal();
    }
}

function updateTotal() {
    var prix = parseFloat(document.getElementById('prix_unit').value) || 0;
    var qte = parseInt(document.querySelector('input[name="quantite"]').value) || 0;
    document.getElementById('total_est').value = (prix * qte).toFixed(2);
}
</script>

<?php require_once '../includes/footer.php'; ?>