<?php
// Générer une facture pour une commande

session_start();
$allowed_roles = ['admin','manager','client'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];
$id = (int)($_GET['id'] ?? 0);

// Récupérer la commande
$stmt = $pdo->prepare('SELECT * FROM commandes WHERE id = ?');
$stmt->execute([$id]);
$commande = $stmt->fetch();

if (!$commande) {
    $_SESSION['error'] = 'Commande introuvable.';
    header('Location: commandes.php');
    exit;
}

// Le client ne peut voir que ses propres commandes
if ($role === 'client' && $commande['client_id'] !== $user_id) {
    $_SESSION['error'] = 'Accès non autorisé.';
    header('Location: commandes.php');
    exit;
}

// Récupérer le client séparément
$stmt = $pdo->prepare('SELECT nom, email FROM users WHERE id = ?');
$stmt->execute([$commande['client_id']]);
$client = $stmt->fetch();

// Récupérer le produit séparément
$stmt = $pdo->prepare('SELECT nom, prix, categorie FROM produits WHERE id = ?');
$stmt->execute([$commande['produit_id']]);
$produit = $stmt->fetch();

// Exportation CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="facture_' . $id . '.csv"');
    $out = fopen('php://output', 'w');
    // BOM pour Excel
    fputs($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Champ', 'Valeur'], ';');
    fputcsv($out, ['Facture N°', 'FAC-' . str_pad($id, 6, '0', STR_PAD_LEFT)], ';');
    fputcsv($out, ['Date', date('d/m/Y H:i', strtotime($commande['created_at']))], ';');
    fputcsv($out, ['Client', $client['nom'] ?? ''], ';');
    fputcsv($out, ['Email', $client['email'] ?? ''], ';');
    fputcsv($out, ['Produit', $produit['nom'] ?? ''], ';');
    fputcsv($out, ['Catégorie', $produit['categorie'] ?? ''], ';');
    fputcsv($out, ['Prix unitaire (TND)', number_format($produit['prix'] ?? 0, 2, ',', '')], ';');
    fputcsv($out, ['Quantité', $commande['quantite']], ';');
    fputcsv($out, ['Total (TND)', number_format($commande['total'], 2, ',', '')], ';');
    fputcsv($out, ['Statut', $commande['statut']], ';');
    fclose($out);
    exit;
}

// Facture HTML
$page_title = 'Facture #' . $id;
$module_color = 'danger';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h2><i class="bi bi-file-earmark-text"></i> Facture</h2>
    <div>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="bi bi-printer"></i> Imprimer
        </button>
        <a href="facture.php?id=<?= $id ?>&export=csv" class="btn btn-outline-secondary ms-1">
            <i class="bi bi-filetype-csv"></i> Exporter CSV
        </a>
        <a href="commande_view.php?id=<?= $id ?>" class="btn btn-outline-secondary ms-1">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="card shadow">
    <div class="card-body p-4">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-6">
                <h3 class="fw-bold text-danger">Usine Industriel</h3>
                <p class="text-muted mb-0">Zone Industrielle Nord<br>Rue de la Fabrique<br>contact@usine.local</p>
            </div>
            <div class="col-6 text-end">
                <h2>FACTURE</h2>
                <p class="mb-0">
                    <strong>N°:</strong> FAC-<?= str_pad($id, 6, '0', STR_PAD_LEFT) ?><br>
                    <strong>Date:</strong> <?= date('d/m/Y', strtotime($commande['created_at'])) ?><br>
                    <strong>Statut:</strong>
                    <span class="badge badge-<?= $commande['statut'] ?>"><?= $commande['statut'] ?></span>
                </p>
            </div>
        </div>

        <hr>

        <!-- Info client -->
        <div class="row mb-4">
            <div class="col-6">
                <h6 class="text-uppercase text-muted">Facturé à</h6>
                <strong><?= htmlspecialchars($client['nom'] ?? '') ?></strong><br>
                <?= htmlspecialchars($client['email'] ?? '') ?>
            </div>
        </div>

        <!-- Lignes de facturation -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Description</th>
                    <th>Catégorie</th>
                    <th class="text-end">Prix unit.</th>
                    <th class="text-end">Quantité</th>
                    <th class="text-end">Total HT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($produit['nom'] ?? '') ?></td>
                    <td><?= htmlspecialchars($produit['categorie'] ?? '') ?></td>
                    <td class="text-end"><?= number_format($produit['prix'] ?? 0, 2, ',', ' ') ?> TND</td>
                    <td class="text-end"><?= $commande['quantite'] ?></td>
                    <td class="text-end fw-bold"><?= number_format($commande['total'], 2, ',', ' ') ?> TND</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end fw-bold">TOTAL</td>
                    <td class="text-end fw-bold fs-5 text-danger"><?= number_format($commande['total'], 2, ',', ' ') ?>
                        TND</td>
                </tr>
            </tfoot>
        </table>

        <div class="text-muted small mt-4">
            <p>Merci de votre confiance. Cette facture a été générée automatiquement par le système Usine Industriel.
            </p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>