<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../services/controller.php");
require_once(__DIR__ . "/../cart/controller.php");

// 1. Détection du rôle d'administration
$isAdmin = isset($_SESSION['user']) && isset($_SESSION['user']['role']) && ($_SESSION['user']['role'] === 'manager');
$quantitesPanier = (!$isAdmin && isset($_SESSION['user'])) ? getCartQuantitiesByUser($_SESSION['user']['usId'], $pdo) : [];

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
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
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

<main class="<?= $isAdmin ? 'admin-main container-fluid px-4' : 'container below-header' ?> my-4">

    <!-- En-tête conditionnel -->
    <div class="<?= $isAdmin ? '' : 'page-banner' ?> d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title text-dark mb-0">
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
    <div class=" row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
        <?php if (empty($categories)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center py-4">
                    Aucune catégorie trouvée.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($categories as $category): ?>
                <?php
                // Chargement des produits et services de la catégorie pour la vue client
                $produitsCategorie = !$isAdmin ? getProduitsByCategorie($category['catId'], $pdo) : [];
                $servicesCategorie = !$isAdmin ? getServicesByCategorie($category['catId'], $pdo) : [];
                $nombreProduits = count($produitsCategorie);
                $nombreServices = count($servicesCategorie);
                ?>
                <div class="col">
                    <!-- Carte Catégorie -->
                    <div class="card h-100 shadow-sm border-0 rounded-3 <?= !$isAdmin ? 'categorie-card' : 'admin-row-card' ?>"
                        <?= !$isAdmin ? 'data-bs-toggle="modal" data-bs-target="#modalDetailCategorie' . $category['catId'] . '" style="cursor: pointer;"' : '' ?>>

                        <div class="card-body d-flex flex-column justify-content-between p-3">
                            <div>
                                <?php if (!empty($category['catImage'])): ?>
                                    <img src="../uploads/categories/<?= htmlspecialchars($category['catImage']) ?>"
47                                        class="w-100 rounded mb-1" style="height: 190px; object-fit: cover;" alt="<?= htmlspecialchars($category['name']) ?>">
                                <?php endif; ?>

                                <h5 class="fw-bold text-dark mb-1">
                                    <?= htmlspecialchars($category['name'] ?? '') ?>
                                </h5>
                                <p class="text-muted small mb-1 produit-description">
                                    <?= htmlspecialchars($category['description'] ?? 'Aucune description disponible.') ?>
                                </p>

                                <?php if (!$isAdmin): ?>
                                    <span class="badge bg-secondary"><?= $nombreProduits ?> produit(s)</span>
                                    <span class="badge bg-secondary"><?= $nombreServices ?> service(s)</span>
                                <?php endif; ?>
                            </div>

                            <!-- Actions ADMIN -->
                            <?php if ($isAdmin): ?>
                                <div class="mt-3 pt-2 border-top d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#changeModal<?= $category['catId'] ?>"
                                            aria-label="Mise à jour" title="Mise à jour">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.114.168l-.803 2.008a.25.25 0 0 0 .32.32l2.008-.803a.5.5 0 0 0 .168-.115l6.813-6.812z" />
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                        </svg>
                                    </button>

                                    <form action="./save.php" method="POST" class="m-0" onsubmit="return confirm('Voulez-vous vraiment supprimer cette catégorie ?');">
                                        <input type="hidden" name="id" value="<?= $category['catId'] ?>">
                                        <button type="submit" name="validate" value="Supprimer" class="btn btn-sm btn-outline-danger"
                                                aria-label="Supprimer" title="Supprimer">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L13.882 4zM2.5 3h11V2h-11z" />
                                            </svg>
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
                                    <div class="row row-cols-2 row-cols-md-3 g-3 cartes-animees">
                                        <?php foreach ($produitsCategorie as $produit): ?>
                                            <div class="col">
                                                <div class="card h-100" style="position: relative;">
                                                    <?php $qte = $quantitesPanier['produit_' . $produit['proId']] ?? 0; ?>
                                                    <span class="panier-badge-carte <?= $qte > 0 ? '' : 'd-none' ?>" data-panier-key="produit_<?= $produit['proId'] ?>">×<?= $qte ?></span>
                                                    <a href="../products/detail.php?id=<?= $produit['proId'] ?>" class="text-decoration-none text-dark">
                                                        <?php if (!empty($produit['image'])): ?>
                                                            <img src="../uploads/products/<?= htmlspecialchars($produit['image']) ?>"
                                                                class="card-img-top" style="height: 100px; object-fit: cover;">
                                                        <?php endif; ?>
                                                        <div class="card-body p-2">
                                                            <p class="mb-1 small fw-semibold"><?= htmlspecialchars($produit['proName']) ?></p>
                                                            <p class="mb-0 small text-primary fw-bold">
                                                                <?= number_format($produit['price'], 0, ',', ' ') ?> FCFA
                                                            </p>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <hr>
                                <p class="fw-bold"><?= $nombreServices ?> service<?= $nombreServices > 1 ? 's' : '' ?> dans cette catégorie</p>

                                <?php if (empty($servicesCategorie)): ?>
                                    <p class="text-muted">Aucun service pour le moment.</p>
                                <?php else: ?>
                                    <div class="row row-cols-2 row-cols-md-3 g-3 cartes-animees">
                                        <?php foreach ($servicesCategorie as $service): ?>
                                            <div class="col">
                                                <div class="card h-100 service-card">
                                                    <?php $qte = $quantitesPanier['service_' . $service['servId']] ?? 0; ?>
                                                    <span class="panier-badge-carte <?= $qte > 0 ? '' : 'd-none' ?>" data-panier-key="service_<?= $service['servId'] ?>">×<?= $qte ?></span>
                                                    <a href="../services/detail.php?id=<?= $service['servId'] ?>" class="text-decoration-none text-dark">
                                                        <?php if (!empty($service['serImage'])): ?>
                                                            <img src="../uploads/services/<?= htmlspecialchars($service['serImage']) ?>"
                                                                class="card-img-top" style="height: 100px; object-fit: cover;">
                                                        <?php endif; ?>
                                                        <div class="card-body p-2">
                                                            <p class="mb-1 small fw-semibold"><?= htmlspecialchars($service['servName']) ?></p>
                                                            <p class="mb-0 small text-primary fw-bold">
                                                                <?= number_format($service['priceHours'], 0, ',', ' ') ?> FCFA/h
                                                            </p>
                                                        </div>
                                                    </a>
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
                                <a href="../services/index.php?catId=<?= $category['catId'] ?>" class="btn btn-outline-warning">
                                    Voir tous les services
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
                                            <img src="../uploads/categories/<?= htmlspecialchars($category['catImage']) ?>"
                                                class="w-100 rounded mb-2" style="height: 100px; object-fit: cover;" alt="">
                                        <?php endif; ?>
                                        <div class="mb-3">
                                            <label class="form-label d-block mb-1">
                                                Image actuelle :
                                                <span class="text-muted"><?= !empty($category['catImage']) ? htmlspecialchars($category['catImage']) : 'aucune' ?></span>
                                            </label>
                                            <label for="catImage_<?= $category['catId'] ?>" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
                                                </svg>
                                                Changer l'image
                                            </label>
                                            <input type="file" name="catImage" id="catImage_<?= $category['catId'] ?>" accept="image/*" class="visually-hidden">
                                            <div class="form-text small text-muted mt-1 fichier-nom"></div>
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
                                        <div class="row row-cols-2 row-cols-md-3 g-3 cartes-animees">
                                            <?php foreach ($produitsCategorie as $produit): ?>
                                                <div class="col">
                                                    <div class="card h-100 border-0 shadow-sm">
                                                        <?php if (!empty($produit['image'])): ?>
                                                            <img src="../uploads/products/<?= htmlspecialchars($produit['image']) ?>"
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
                                <label for="catImage" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                        <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
                                    </svg>
                                    Ajouter une image
                                </label>
                                <input type="file" name="catImage" id="catImage" accept="image/*" class="visually-hidden">
                                <div class="form-text small text-muted mt-1 fichier-nom"></div>
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