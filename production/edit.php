<?php
// Modifier un ordre de production

session_start();
$allowed_roles = ['admin','manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

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

// Récupérer la liste des employés
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
            'nom' => $user['nom']
        ];
    }
}

$page_title = 'Modifier ordre';
$module_color = 'primary';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Modifier ordre #<?= $ordre['id'] ?></h2>
    <a href="view.php?id=<?= $ordre['id'] ?>" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i>
        Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="edit_process.php">
            <input type="hidden" name="id" value="<?= $ordre['id'] ?>">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" class="form-control" required
                        value="<?= htmlspecialchars($ordre['titre']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select" required>
                        <option value="en_attente" <?= $ordre['statut']==='en_attente'?'selected':'' ?>>En attente
                        </option>
                        <option value="en_cours" <?= $ordre['statut']==='en_cours'?'selected':'' ?>>En cours</option>
                        <option value="termine" <?= $ordre['statut']==='termine'?'selected':'' ?>>Terminé</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"
                        rows="4"><?= htmlspecialchars($ordre['description'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Employé assigné</label>
                    <select name="employe_id" class="form-select">
                        <option value="">— Non assigné —</option>
                        <?php foreach ($employes as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $ordre['employe_id']==$e['id']?'selected':'' ?>>
                            <?= htmlspecialchars($e['nom']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>