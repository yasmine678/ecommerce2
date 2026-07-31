<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/controller.php");

// 1. Détection du rôle d'administration
$isAdmin = isset($_SESSION['user']) && isset($_SESSION['user']['role']) && ($_SESSION['user']['role'] === 'manager');

// 2. Récupération de toutes les catégories
$categories = getAll($pdo);


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isAdmin ? "Gestion des Catégories" : "Nos Catégories" ?> - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/bootstrap.js" defer></script>
</head>

<body class="<?= $isAdmin ? 'admin bg-light' : 'client bg-light' ?>"></body>

<!-- Inclusion conditionnelle du Header ou Sidebar -->
<?php
if ($isAdmin) {
    include(__DIR__ . "/../admin/sidebar_admin.php");
} else {
    include(__DIR__ . "/../includes/header.php");
}
?>

<main class="<?= $isAdmin ? 'container-fluid px-4' : 'container below-header' ?> my-4"
    style="<?= $isAdmin ? 'margin-left: 250px; width: calc(100% - 250px);' : '' ?>">

    <!-- En-tête conditionnel -->
    <div class=" d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">
                <?= $isAdmin ? "Gestion des catégories" : "Nos Catégories" ?>
            </h2>
            <p class="text-muted small mb-0">
                <?= $isAdmin ? "Espace d'administration des thématiques" : "Parcourez notre catalogue par thématique" ?>
            </p>
        </div>

        <!-- Bouton d'ajout visible uniquement par l'Admin -->
        <?php if ($isAdmin): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                + Ajouter 
            </button>
        <?php endif; ?>
    </div>

    <!-- Liste des catégories -->
    <div class=" row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
        <?php if (empty($categories)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center py-4">
                    Aucune catégorie trouvée.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($categories as $category): ?>
                <?php
                // Chargement des produits de la catégorie pour la vue client
                $produitsCategorie = !$isAdmin ? getProduitsByCategorie($category['catId'], $pdo) : [];
                $nombreProduits = count($produitsCategorie);
                ?>
                <div class="col">
                    <!-- Carte Catégorie -->
                    <div class="card h-100 shadow-sm border-0 rounded-3 <?= !$isAdmin ? 'categorie-card' : '' ?>"
                        <?= !$isAdmin ? 'data-bs-toggle="modal" data-bs-target="#modalDetailCategorie' . $category['catId'] . '" style="cursor: pointer;"' : '' ?>>

                        <div class="card-body d-flex flex-column justify-content-between p-3">
                            <div>
                                <?php if (!empty($category['catImage'])): ?>
                                    <img src="../assets/images/<?= htmlspecialchars($category['catImage']) ?>"
                                        class="w-100 rounded mb-1" style="height: 250px; object-fit: cover;" alt="<?= htmlspecialchars($category['name']) ?>">
                                <?php endif; ?>

                                <h5 class="fw-bold text-dark mb-1">
                                    <?= htmlspecialchars($category['name'] ?? '') ?>
                                </h5>
                                <p class="text-muted small mb-1">
                                    <?= htmlspecialchars($category['description'] ?? 'Aucune description disponible.') ?>
                                </p>

                                <?php if (!$isAdmin): ?>
                                    <span class="badge bg-secondary"><?= $nombreProduits ?> produit(s)</span>
                                <?php endif; ?>
                            </div>

                            <!-- Actions ADMIN -->
                            <?php if ($isAdmin): ?>
                                <div class="mt-3 pt-2 border-top d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#changeModal<?= $category['catId'] ?>">
                                        Mise à jour
                                    </button>

                                    <form action="./save.php" method="POST" class="m-0" onsubmit="return confirm('Voulez-vous vraiment supprimer cette catégorie ?');">
                                        <input type="hidden" name="id" value="<?= $category['catId'] ?>">
                                        <button type="submit" name="validate" value="Supprimer" class="btn btn-sm btn-outline-danger">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
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
                                                        <img src="../assets/images/<?= htmlspecialchars($produit['image']) ?>"
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
                                <a href="../products/index.php?catId=<?= $category['catId'] ?>" class="btn btn-warning">
                                    Voir tous les produits
                                </a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODALES SELON LE RÔLE -->

                <?php if ($isAdmin): ?>
                    <!-- MODAL ADMIN : Mise à jour -->
                    <div class="modal fade" id="changeModal<?= $category['catId'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="./save.php" method="POST" enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Mise à jour de la catégorie</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $category['catId'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nom :</label>
                                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name'] ?? ''); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Description :</label>
                                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($category['description'] ?? ''); ?></textarea>
                                        </div>
                                        <?php if (!empty($category['catImage'])): ?>
                                            <img src="../assets/images/<?= htmlspecialchars($category['catImage']) ?>"
                                                class="w-100 rounded mb-2" style="height: 100px; object-fit: cover;" alt="">
                                        <?php endif; ?>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Changer l'image :</label>
                                            <input type="file" name="catImage" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" name="validate" value="mise à jour" class="btn btn-primary">Modifier</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- MODAL CLIENT : Détail de la catégorie -->
                    <div class="modal fade" id="modalDetailCategorie<?= $category['catId'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold"><?= htmlspecialchars($category['name']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
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
                                                    <div class="card h-100 border-0 shadow-sm">
                                                        <?php if (!empty($produit['image'])): ?>
                                                            <img src="../assets/images/<?= htmlspecialchars($produit['image']) ?>"
                                                                class="card-img-top" style="height: 100px; object-fit: cover;" alt="<?= htmlspecialchars($produit['proName']) ?>">
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

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- MODAL ADMIN UNIQUE : Ajout d'une catégorie -->
    <?php if ($isAdmin): ?>
        <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="./save.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Ajouter une catégorie</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nom de la catégorie</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="desc" class="form-label fw-semibold">Description</label>
                                <textarea name="description" id="desc" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="catImage" class="form-label fw-semibold">Image de la catégorie</label>
                                <input type="file" name="catImage" id="catImage" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" name="validate" value="Creer" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

</main>

</body>

</html>