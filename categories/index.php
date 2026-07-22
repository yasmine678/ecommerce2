<?php
session_start();
require_once("../config/db.php");
require_once("./controller.php");

// 1. Récupération des catégories
$categories = getAll($pdo);

// 2. Vérification du rôle de l'utilisateur
// S'il est connecté et que son rôle est 'admin', $isAdmin sera vrai (true)
$isAdmin = isset($_SESSION['user']) && ($_SESSION['user']['role'] === 'admin');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/bootstrap .js"></script>
</head>


<body style="padding-top: 90px; background-color: #f8f9fa;">

    <!-- En-tête dynamique selon le rôle -->
    <?php
    include("../includes/header.php");

    ?>

    <div class="container my-4">

        <!-- En-tête de page -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">
                    <?= $isAdmin ? "Gestion des catégories" : "Nos Catégories" ?>
                </h2>
                <p class="text-muted small mb-0">Parcourez notre catalogue par thématique</p>
            </div>

        </div>

        <!-- Liste des catégories -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            <?php foreach ($categories as $category): ?>
                <?php
                $produitsCategorie = getProduitsByCategorie($category['catId'], $pdo);
                $nombreProduits = count($produitsCategorie);
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 rounded-3 categorie-card"
                        data-bs-toggle="modal"
                        data-bs-target="#modalDetailCategorie<?= $category['catId'] ?>"
                        ondblclick="ouvrirDetailCategorie(<?= $category['catId'] ?>)"
                        style="cursor: pointer;">
                        <div class="card-body">
                            <?php if (!empty($category['image'])): ?>
                                <img src="/ecommerce/assets/images/categories/<?= htmlspecialchars($category['image']) ?>"
                                    class="w-100 rounded mb-2" style="height: 120px; object-fit: cover;">
                            <?php endif; ?>
                            <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($category['name']) ?></h5>
                            <p class="text-muted small mb-1"><?= htmlspecialchars($category['description'] ?? '') ?></p>
                            <span class="badge bg-secondary"><?= $nombreProduits ?> produit(s)</span>
                        </div>
                    </div>
                </div>

                <!-- MODALE : détail de la catégorie (ouverte au double-clic) -->
                <div class="modal fade" id="modalDetailCategorie<?= $category['catId'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= htmlspecialchars($category['name']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted"><?= htmlspecialchars($category['description'] ?? 'Aucune description.') ?></p>
                                <p class="fw-bold"><?= $nombreProduits ?> produit<?= $nombreProduits > 1 ? 's' : '' ?> dans cette catégorie</p>

                                <?php if (empty($produitsCategorie)): ?>
                                    <p class="text-muted">Aucun produit pour le moment.</p>
                                <?php else: ?>
                                    <div class="row row-cols-2 row-cols-md-3 g-3">
                                        <?php foreach ($produitsCategorie as $produit): ?>
                                            <div class="col">
                                                <div class="card h-100">
                                                    <?php if (!empty($produit['image'])): ?>
                                                        <img src="/ecommerce/assets/images/<?= htmlspecialchars($produit['image']) ?>"
                                                            class="card-img-top" style="height: 100px; object-fit: cover;">
                                                    <?php endif; ?>
                                                    <div class="card-body p-2">
                                                        <p class="mb-1 small fw-semibold"><?= htmlspecialchars($produit['proName']) ?></p>
                                                        <p class="mb-0 small text-primary fw-bold">
                                                            <?= number_format($produit['price'], 0, ',', ' ') ?> FCFA
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer">
                                <a href="./produits.php?catId=<?= $category['catId'] ?>" class="btn btn-warning">
                                    Voir tous les produits
                                </a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

</body>

</html>