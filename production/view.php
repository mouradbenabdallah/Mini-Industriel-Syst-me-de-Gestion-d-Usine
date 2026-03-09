<?php
// Détail d'un ordre de production

session_start();
$allowed_roles = ['admin','manager','employe'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];
$id = (int)($_GET['id'] ?? 0);

// Récupérer l'ordre
$stmt = $pdo->prepare('SELECT * FROM ordres WHERE id=?');
$stmt->execute([$id]);
$ordre = $stmt->fetch();

if (!$ordre) {
    $_SESSION['error'] = 'Ordre introuvable.';
    header('Location: index.php');
    exit;
}

// Vérifier si l'employé peut voir cet ordre
if ($role === 'employe') {
    $stmt = $pdo->prepare('SELECT user_id FROM employes WHERE id = ?');
    $stmt->execute([$ordre['employe_id']]);
    $emp = $stmt->fetch();
    if (!$emp || $emp['user_id'] !== $user_id) {
        $_SESSION['error'] = 'Accès non autorisé.';
        header('Location: index.php');
        exit;
    }
}

// Récupérer le nom de l'employé séparément
$employe_nom = '';
if ($ordre['employe_id']) {
    $stmt = $pdo->prepare('SELECT user_id FROM employes WHERE id = ?');
    $stmt->execute([$ordre['employe_id']]);
    $emp = $stmt->fetch();
    if ($emp) {
        $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
        $stmt->execute([$emp['user_id']]);
        $user = $stmt->fetch();
        $employe_nom = $user ? $user['nom'] : '';
    }
}

// Récupérer le nom du manager séparément
$manager_nom = '';
if ($ordre['manager_id']) {
    $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
    $stmt->execute([$ordre['manager_id']]);
    $manager = $stmt->fetch();
    $manager_nom = $manager ? $manager['nom'] : '';
}

// Déterminer le prochain statut possible
$next_status = null;
$state_machine = [
    'en_attente' => 'en_cours',
    'en_cours' => 'termine',
];
if (isset($state_machine[$ordre['statut']])) {
    $next_status = $state_machine[$ordre['statut']];
}

// Vérifier si l'utilisateur peut avancer le statut
$can_advance = false;
if ($next_status) {
    if (in_array($role, ['admin','manager'])) {
        $can_advance = true;
    } elseif ($role === 'employe' && $ordre['employe_id']) {
        $stmt = $pdo->prepare('SELECT user_id FROM employes WHERE id = ?');
        $stmt->execute([$ordre['employe_id']]);
        $emp = $stmt->fetch();
        if ($emp && $emp['user_id'] === $user_id) {
            $can_advance = true;
        }
    }
}

$page_title = 'Ordre #' . $ordre['id'];
$module_color = 'primary';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-earmark-text"></i> Ordre #<?= $ordre['id'] ?></h2>
    <div>
        <?php if (in_array($role, ['admin','manager'])): ?>
        <a href="edit.php?id=<?= $ordre['id'] ?>" class="btn btn-outline-primary">
            <i class="bi bi-pencil"></i> Modifier
        </a>
        <?php endif; ?>
        <a href="index.php" class="btn btn-outline-secondary ms-1"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong><?= htmlspecialchars($ordre['titre']) ?></strong>
                <span class="badge badge-<?= $ordre['statut'] ?> fs-6"><?= $ordre['statut'] ?></span>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($ordre['description'] ?? 'Aucune description.')) ?></p>
                <hr>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Employé assigné</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($employe_nom ?: 'Non assigné') ?></dd>
                    <dt class="col-sm-4">Manager</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($manager_nom ?: '-') ?></dd>
                    <dt class="col-sm-4">Créé le</dt>
                    <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($ordre['created_at'])) ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <?php if ($can_advance): ?>
        <div class="card border-primary">
            <div class="card-header bg-primary text-white"><i class="bi bi-arrow-right-circle"></i> Avancer le statut
            </div>
            <div class="card-body text-center">
                <p>Passer de <strong><?= $ordre['statut'] ?></strong> à <strong><?= $next_status ?></strong> ?</p>
                <form method="post" action="status_process.php">
                    <input type="hidden" name="id" value="<?= $ordre['id'] ?>">
                    <input type="hidden" name="next_status" value="<?= $next_status ?>">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg"></i> Confirmer
                    </button>
                </form>
            </div>
        </div>
        <?php elseif ($ordre['statut'] === 'termine'): ?>
        <div class="card border-success">
            <div class="card-body text-center text-success">
                <i class="bi bi-check-circle-fill" style="font-size:3rem;"></i>
                <p class="mt-2 mb-0 fw-bold">Ordre terminé</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>