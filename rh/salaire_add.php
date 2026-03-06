<?php
// Ajouter un salaire

session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Récupérer la liste des employés avec leur salaire de base
$stmt = $pdo->query('SELECT * FROM employes ORDER BY id');
$employes_list = $stmt->fetchAll();

// Pour chaque employé, récupérer son nom séparément
$employes = [];
foreach ($employes_list as $e) {
    $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ? AND actif = 1');
    $stmt->execute([$e['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $employes[] = [
            'id' => $e['id'],
            'nom' => $user['nom'],
            'salaire_base' => $e['salaire_base']
        ];
    }
}

$page_title = 'Ajouter un salaire';
$module_color = 'success';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cash"></i> Ajouter un salaire</h2>
    <a href="salaires.php" class="btn btn-outline-success"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="salaire_add_process.php">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employé</label>
                    <select name="employe_id" class="form-select" required onchange="updateMontant()">
                        <option value="">— Choisir —</option>
                        <?php foreach ($employes as $e): ?>
                        <option value="<?= $e['id'] ?>" data-montant="<?= $e['salaire_base'] ?>">
                            <?= htmlspecialchars($e['nom']) ?> (base:
                            <?= number_format($e['salaire_base'], 2, ',', ' ') ?> TND)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mois</label>
                    <input type="month" name="mois" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Montant (TND)</label>
                    <input type="number" name="montant" class="form-control" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date paiement</label>
                    <input type="date" name="date_paiement" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function updateMontant() {
    var select = document.querySelector('select[name="employe_id"]');
    var montant = select.options[select.selectedIndex].getAttribute('data-montant');
    if (montant) {
        document.querySelector('input[name="montant"]').value = montant;
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>