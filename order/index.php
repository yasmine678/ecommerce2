<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$commandes = getCommandesByUser($_SESSION['user']['usId'], $pdo);

$libelles = [
    'en_attente' => ['label' => 'En attente', 'classe' => 'secondary'],
    'en_cours' => ['label' => 'En cours', 'classe' => 'info'],
    'expediee' => ['label' => 'Expédiée', 'classe' => 'primary'],
    'livee' => ['label' => 'Livrée', 'classe' => 'success'],
    'annulee' => ['label' => 'Annulée', 'classe' => 'danger'],
];

$libellesMethodePaiement = [
    'carte' => 'Carte bancaire',
    'flooz' => 'Flooz',
    'tmoney' => 'T-Money',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes commandes - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>
<body class="client">
    <?php include(__DIR__ . "/../includes/header.php"); ?>

    <div class="container my-5 below-header">
        <h2 class="mb-4">Mes commandes</h2>

        <?php if (isset($_GET['succes'])): ?>
            <div class="alert alert-success">
                Votre commande a bien été enregistrée<?= isset($_GET['paye']) ? ' et payée' : '' ?> !
            </div>
        <?php endif; ?>

        <?php if (empty($commandes)): ?>
            <p class="text-muted">Vous n'avez pas encore passé de commande.</p>
            <a href="../products/index.php" class="btn btn-warning">Voir les produits</a>
        <?php else: ?>
            <?php foreach ($commandes as $commande): ?>
                <?php
                $statutInfo = $libelles[$commande['status']] ?? ['label' => $commande['status'], 'classe' => 'secondary'];
                $lignes = getLignesCommande($commande['cmdId'], $pdo);
                $total = 0;
                foreach ($lignes as $ligne) {
                    $total += $ligne['price'] * $ligne['quantity'];
                }
                ?>
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <span class="fw-bold">Commande #<?= $commande['cmdId'] ?></span>
                            <span class="text-muted small">
                                — <?= date('d/m/Y à H:i', strtotime($commande['createdAt'])) ?>
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-<?= $statutInfo['classe'] ?>"><?= $statutInfo['label'] ?></span>
                            <?php if (($commande['paymentStatus'] ?? 'non_payee') === 'payee'): ?>
                                <span class="badge bg-success">
                                    Payée<?= !empty($commande['paymentMethod']) ? ' · ' . htmlspecialchars($libellesMethodePaiement[$commande['paymentMethod']] ?? $commande['paymentMethod']) : '' ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Non payée</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Article</th>
                                    <th>Quantité</th>
                                    <th class="text-end">Sous-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lignes as $ligne): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($ligne['proName'] ?? 'Article supprimé') ?></td>
                                        <td><?= $ligne['quantity'] ?></td>
                                        <td class="text-end"><?= number_format($ligne['price'] * $ligne['quantity'], 0, ',', ' ') ?> FCFA</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end fw-bold">Total :</td>
                                    <td class="text-end fw-bold text-warning"><?= number_format($total, 0, ',', ' ') ?> FCFA</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include(__DIR__ . "/../includes/footer.php"); ?>
    <script src="../assets/js/bootstrap.js"></script>
</body>
</html>
