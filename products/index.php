<?php
session_start();
require_once("../config/db.php");
require_once("./controller.php");
require_once("../categories/controller.php");

// Utilisation de strtolower() et trim() pour éviter les erreurs de majuscule ou d'espaces
$categories = getAll($pdo);
$isAdmin = isset($_SESSION['user']['role']) && (strtolower(trim($_SESSION['user']['role'])) === 'admin');
// 2. Récupération des données selon le paramètre URL
$categoryId = $_GET['category_id'] ?? null;

if ($categoryId) {
    $stmt = $pdo->prepare("SELECT * FROM product WHERE catId = ?");
    $stmt->execute([$categoryId]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = getAllPro($pdo);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body style="padding-top: 90px; background-color: #f8f9fa;">

    <!-- En-tête dynamique (Admin vs Client) -->
    <?php
    include("../includes/header.php");
    ?>

    <div class="container my-4">

        <!-- En-tête de la page -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">
                    <?= $isAdmin ? "Gestion des produits" : "Nos Produits" ?>
                </h2>
                <?php if (!$isAdmin): ?>
                    <p class="text-muted small mb-0">Découvrez nos articles disponibles</p>
                <?php endif; ?>
            </div>

        </div>

        <!-- Liste des produits -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            <?php foreach ($products as $product): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 rounded-3">
                        <div class="card-body d-flex flex-column justify-content-between p-3">

                            <!-- Informations sur le produit -->
                            <div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <?= htmlspecialchars($product['proName'] ?? $product['name'] ?? '') ?>
                                </h5>
                                <p class="text-muted small mb-2">
                                    <?= htmlspecialchars($product['prodescription'] ?? $product['description'] ?? 'Aucune description disponible.') ?>
                                </p>
                                <span class="fw-bold text-primary fs-5">
                                    <?= number_format($product['price'] ?? 0, 0, ',', ' ') ?> FCFA
                                </span>
                                <span class="badge bg-info text-dark bg-opacity-25">
                                    <?= htmlspecialchars($product['catName'] ?? 'Sans catégorie') ?>
                                </span>
                            </div>

                            <!-- Actions selon le rôle -->
                            <div class="mt-3 pt-2 border-top">

                                <form action="../cart/add.php" method="POST" class="m-0">
                                    <input type="hidden" name="product_id" value="<?= $product['proId'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                        🛒 Ajouter au panier
                                    </button>
                                </form>


                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

</body>

</html>