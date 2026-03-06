<?php
// Ajouter une demande de congé

session_start();
$allowed_roles = ['employe'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$user_id = $_SESSION['user']['id'];

// Vérifier si l'utilisateur a un profil employé
$stmt = $pdo->prepare('SELECT id FROM employes WHERE user_id = ?');
$stmt->execute([$user_id]);
$employe = $stmt->fetch();

if (!$employe) {
    $_SESSION['error'] = 'Vous devez avoir un profil employé pour demander un congé.';
    header('Location: conges.php');
    exit;
}

$page_title = 'Demander un congé';
$module_color = 'success';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-calendar-plus"></i> Demander un congé</h2>
    <a href="conges.php" class="btn btn-outline-success"><i class="bi bi-arrow-left"></i> Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="conge_add_process.php">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Date de début</label>
                    <input type="date" name="date_debut" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date de fin</label>
                    <input type="date" name="date_fin" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Motif</label>
                    <textarea name="motif" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Soumettre la demande</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>