<?php
// Historique des mouvements de stock

session_start();
$allowed_roles = ['admin','manager','employe'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Récupérer les filtres
$type_filter = $_GET['type'] ?? '';
$date_filter = $_GET['date'] ?? '';

// Préparer les conditions WHERE
$where = [];
$params = [];

if (in_array($type_filter, ['entree','sortie'])) {
    $where[] = 'type = ?';
    $params[] = $type_filter;
}
if ($date_filter && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_filter)) {
    $where[] = 'DATE(created_at) = ?';
    $params[] = $date_filter;
}

// Requête simple sans JOIN
$sql = 'SELECT * FROM mouvements';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY created_at DESC LIMIT 200';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mouvements = $stmt->fetchAll();

// Pour chaque mouvement, récupérer les infos du produit et de l'utilisateur séparément
$produits_cache = [];
$users_cache = [];
foreach ($mouvements as &$m) {
    // Nom du produit
    if (!isset($produits_cache[$m['produit_id']])) {
        $stmt = $pdo->prepare('SELECT nom FROM produits WHERE id = ?');
        $stmt->execute([$m['produit_id']]);
        $p = $stmt->fetch();
        $produits_cache[$m['produit_id']] = $p['nom'] ?? 'Inconnu';
    }
    $m['produit_nom'] = $produits_cache[$m['produit_id']];
    
    // Nom de l'utilisateur
    if (!isset($users_cache[$m['user_id']])) {
        $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
        $stmt->execute([$m['user_id']]);
        $u = $stmt->fetch();
        $users_cache[$m['user_id']] = $u['nom'] ?? 'Inconnu';
    }
    $m['user_nom'] = $users_cache[$m['user_id']];
}

$page_title = 'Historique des mouvements';
$module_color = 'warning';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clock-history"></i> Historique des mouvements</h2>
    <a href="index.php" class="btn btn-outline-warning"><i class="bi bi-arrow-left"></i> Retour stock</a>
</div>

<form method="get" class="row g-2 mb-4">
    <div class="col-md-3">
        <select name="type" class="form-select">
            <option value="">Tous les types</option>
            <option value="entree" <?= $type_filter==='entree'?'selected':'' ?>>Entrées</option>
            <option value="sortie" <?= $type_filter==='sortie'?'selected':'' ?>>Sorties</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date_filter) ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-warning"><i class="bi bi-funnel"></i> Filtrer</button>
        <a href="historique.php" class="btn btn-outline-secondary ms-1">Réinitialiser</a>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-warning">
                <tr>
                    <th>Date</th>
                    <th>Produit</th>
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
                        <a href="view.php?id=<?= $m['produit_id'] ?>"><?= htmlspecialchars($m['produit_nom']) ?></a>
                    </td>
                    <td>
                        <span class="badge bg-<?= $m['type']==='entree'?'success':'danger' ?>">
                            <?= $m['type']==='entree' ? '↑ Entrée' : '↓ Sortie' ?>
                        </span>
                    </td>
                    <td><?= $m['quantite'] ?></td>
                    <td><?= htmlspecialchars($m['note'] ?? '') ?></td>
                    <td><?= htmlspecialchars($m['user_nom']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$mouvements): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">Aucun mouvement trouvé.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>