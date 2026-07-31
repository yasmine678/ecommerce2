<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: /ecommerce/auth/login.php");
    exit();
}

$userId = $_SESSION['user']['usId'];
$lignesCart = getCartByUser($userId, $pdo);

$montantTotal = 0;
foreach ($lignesCart as $ligne) {
    $montantTotal += $ligne['price'] * $ligne['cquantity'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier - YosiShop</title>
    <link rel="stylesheet" href="/ecommerce/assets/css/bootstrap.css">
    <link rel="stylesheet" href="/ecommerce/assets/css/style.css">
</head>
<body class="client">
    <?php include(__DIR__ . "/../includes/header.php"); ?>

    <div class="container my-5">
        <h2 class="mb-4">Mon panier</h2>

        <?php if (isset($_GET['succes'])): ?>
            <div class="alert alert-success">Panier mis à jour.</div>
        <?php endif; ?>

        <?php if (empty($lignesCart)): ?>
            <p class="text-muted">Votre panier est vide.</p>
            <a href="/ecommerce/products/index.php" class="btn btn-warning">Voir les produits</a>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Sous-total</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lignesCart as $ligne): ?>
                            <tr>
                                <td class="d-flex align-items-center gap-2">
                                    <?php if (!empty($ligne['image'])): ?>
                                        <img src="/ecommerce/assets/images/<?= htmlspecialchars($ligne['image']) ?>"
                                             style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                    <?php endif; ?>
                                    <?= htmlspecialchars($ligne['proName']) ?>
                                </td>
                                <td><?= number_format($ligne['price'], 0, ',', ' ') ?> FCFA</td>
                                <td style="width: 140px;">
                                    <form action="./save.php" method="POST" class="d-flex gap-2">
                                        <input type="hidden" name="cId" value="<?= $ligne['cId'] ?>">
                                        <input type="number" name="cquantity" value="<?= $ligne['cquantity'] ?>"
                                               min="1" class="form-control form-control-sm">
                                        <button type="submit" name="validate" value="Modifier"
                                                class="btn btn-sm btn-outline-primary">OK</button>
                                    </form>
                                </td>
                                <td class="fw-bold">
                                    <?= number_format($ligne['price'] * $ligne['cquantity'], 0, ',', ' ') ?> FCFA
                                </td>
                                <td class="text-end">
                                    <form action="./save.php" method="POST"
                                          onsubmit="return confirm('Retirer ce produit du panier ?');">
                                        <input type="hidden" name="cId" value="<?= $ligne['cId'] ?>">
                                        <button type="submit" name="validate" value="Supprimer"
                                                class="btn btn-sm btn-outline-danger">Retirer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total :</td>
                            <td colspan="2" class="fw-bold text-warning fs-5">
                                <?= number_format($montantTotal, 0, ',', ' ') ?> FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <form action="/ecommerce/order/create.php" method="POST">
                <div class="mb-3">
                    <label for="note" class="form-label">
                        Une précision pour le vendeur ? (optionnel)
                    </label>
                    <textarea name="note" id="note" class="form-control" rows="3"
                        placeholder="Ex : couleur souhaitée, adresse de livraison précise, créneau horaire..."></textarea>
                    <div class="form-text">Ce message ne sera visible que par le manager.</div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-warning btn-lg">
                        Passer la commande
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <?php include(__DIR__ . "/../includes/footer.php"); ?>
    <script src="../assets/js/bootstrap.js"></script>
</body>
</html>