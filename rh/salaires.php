<?php
// Liste des salaires

session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Récupérer les filtres
$employe_id = $_GET['employe_id'] ?? '';
$mois = $_GET['mois'] ?? '';

// Construire la requête
$where = [];
$params = [];

if ($employe_id) {
    $where[] = 's.employe_id = ?';
    $params[] = $employe_id;
}
if ($mois) {
    $where[] = 's.mois = ?';
    $params[] = $mois;
}

// Récupérer les salaires
if ($where) {
    $sql = 'SELECT s.* FROM salaires s WHERE ' . implode(' AND ', $params);
} else {
    $sql = 'SELECT * FROM salaires';
}
$sql .= ' ORDER BY created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$salaires_list = $stmt->fetchAll();

// Pour chaque salaire, récupérer le nom de l'employé séparément
$salaires = [];
foreach ($salaires_list as $s) {
    $stmt = $pdo->prepare('SELECT user_id FROM employes WHERE id = ?');
    $stmt->execute([$s['employe_id']]);
    $emp = $stmt->fetch();
    if ($emp) {
        $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
        $stmt->execute([$emp['user_id']]);
        $user = $stmt->fetch();
        $salaires[] = [
            'id' => $s['id'],
            'employe_nom' => $user ? $user['nom'] : '',
            'mois' => $s['mois'],
            'montant' => $s['montant'],
            'date_paiement' => $s['date_paiement']
        ];
    }
}

// Récupérer la liste des employés pour le filtre
$stmt = $pdo->query('SELECT id, user_id FROM employes ORDER BY id');
$employes_list = $stmt->fetchAll();

$employes = [];
foreach ($employes_list as $e) {
    $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
    $stmt->execute([$e['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $employes[] = [
            'id' => $e['id'],
            'nom' => $user['nom']
        ];
    }
}

$page_title = 'Salaires';
$module_color = 'success';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cash"></i> Gestion des salaires</h2>
    <a href="salaire_add.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Ajouter un salaire</a>
</div>

<!-- Filtres -->
<div class="mb-3">
    <form method="get" class="row g-3">
        <div class="col-md-4">
            <select name="employe_id" class="form-select">
                <option value="">Tous les employés</option>
                <?php foreach ($employes as $e): ?>
                <option value="<?= $e['id'] ?>" <?= $employe_id==$e['id']?'selected':'' ?>>
                    <?= htmlspecialchars($e['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="month" name="mois" class="form-control" value="<?= $mois ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-secondary w-100">Filtrer</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-success">
                <tr>
                    <th>Employé</th>
                    <th>Mois</th>
                    <th>Montant</th>
                    <th>Date paiement</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salaires as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['employe_nom']) ?></td>
                    <td><?= $s['mois'] ?></td>
                    <td class="fw-bold"><?= number_format($s['montant'], 2, ',', ' ') ?> TND</td>
                    <td><?= date('d/m/Y', strtotime($s['date_paiement'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$salaires): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">Aucun salaire trouvé.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>