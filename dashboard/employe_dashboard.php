<?php
// Tableau de bord Employé - Espace personnel pour les employés

session_start();
$allowed_roles = ['employe'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$user_id = $_SESSION['user']['id'];

// Chercher si l'employé existe dans la table employes
$stmt = $pdo->prepare('SELECT * FROM employes WHERE user_id = ?');
$stmt->execute([$user_id]);
$employe = $stmt->fetch();

// Si l'employé n'existe pas, on le crée automatiquement
if (!$employe) {
    $stmt = $pdo->prepare('INSERT INTO employes (user_id, poste, salaire_base) VALUES (?, ?, ?)');
    $stmt->execute([$user_id, 'Employé', 1500]);
    
    // Récupérer l'employé nouvellement créé
    $employe_id = $pdo->lastInsertId();
    $stmt = $pdo->prepare('SELECT * FROM employes WHERE id = ?');
    $stmt->execute([$employe_id]);
    $employe = $stmt->fetch();
}

// Récupérer les ordres de production de l'employé
$stmt = $pdo->prepare('SELECT * FROM ordres WHERE employe_id = ? ORDER BY created_at DESC LIMIT 10');
$stmt->execute([$employe['id']]);
$mes_ordres = $stmt->fetchAll();

// Récupérer les demandes de congés
$stmt = $pdo->prepare('SELECT * FROM conges WHERE employe_id = ? ORDER BY created_at DESC LIMIT 5');
$stmt->execute([$employe['id']]);
$mes_conges = $stmt->fetchAll();

$page_title = 'Mon espace - Employé';
$module_color = 'primary';
require_once '../includes/header.php';
?>

<h2 class="mb-4"><i class="bi bi-person-workspace"></i> Mon espace - Employé</h2>

<!-- Informations de l'employé -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-center bg-primary text-white">
            <div class="card-body py-3">
                <h4><?= htmlspecialchars($employe['poste']) ?></h4>
                <small>Poste</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-success text-white">
            <div class="card-body py-3">
                <h4><?= number_format($employe['salaire_base'], 2, ',', ' ') ?> TND</h4>
                <small>Salaire de base</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Mes ordres de production -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-wrench"></i> Mes ordres de production</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mes_ordres as $o): ?>
                        <tr>
                            <td><?= htmlspecialchars($o['titre']) ?></td>
                            <td><span class="badge badge-<?= $o['statut'] ?>"><?= $o['statut'] ?></span></td>
                            <td><?= date('d/m/Y', strtotime($o['created_at'])) ?></td>
                            <td><a href="../production/view.php?id=<?= $o['id'] ?>"
                                    class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (!$mes_ordres): ?><tr>
                            <td colspan="4" class="text-center text-muted">Aucun ordre assigné</td>
                        </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="../production/index.php" class="btn btn-sm btn-primary">Voir tout</a>
            </div>
        </div>
    </div>

    <!-- Mes demandes de congés -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar-x"></i> Mes demandes de congés</div>
            <ul class="list-group list-group-flush">
                <?php foreach ($mes_conges as $c): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?= date('d/m', strtotime($c['date_debut'])) ?> -
                        <?= date('d/m/Y', strtotime($c['date_fin'])) ?></span>
                    <span class="badge badge-<?= $c['statut'] ?>"><?= $c['statut'] ?></span>
                </li>
                <?php endforeach; ?>
                <?php if (!$mes_conges): ?><li class="list-group-item text-muted text-center">Aucune demande</li>
                <?php endif; ?>
            </ul>
            <div class="card-footer">
                <a href="../rh/conge_add.php" class="btn btn-sm btn-success">Nouvelle demande</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>