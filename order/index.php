<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: /ecommerce/auth/login.php");
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
            <div class="alert alert-success">Votre commande a bien été enregistrée !</div>
        <?php endif; ?>

        <?php if (empty($commandes)): ?>
            <p class="text-muted">Vous n'avez pas encore passé de commande.</p>
            <a href="/ecommerce/products/index.php" class="btn btn-warning">Voir les produits</a>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                          
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $commande): ?>
                            <?php $statutInfo = $libelles[$commande['status']] ?? ['label' => $commande['status'], 'classe' => 'secondary']; ?>
                            <tr>
                                
                                <td><?= htmlspecialchars($commande['proName']) ?></td>
                                <td><?= $commande['quantity'] ?></td>
                                <td><?= number_format($commande['price'] * $commande['quantity'], 0, ',', ' ') ?> FCFA</td>
                                <td>
                                    <span class="badge bg-<?= $statutInfo['classe'] ?>"><?= $statutInfo['label'] ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php include(__DIR__ . "/../includes/footer.php"); ?>
    <script src="../assets/js/bootstrap.js"></script>
</body>
</html>