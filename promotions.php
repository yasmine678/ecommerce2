<?php
session_start();

require_once(__DIR__ . "/products/controller.php");
require_once(__DIR__ . "/services/controller.php");
require_once(__DIR__ . "/config/db.php");
require_once(__DIR__ . "/cart/controller.php");

$quantitesPanier = isset($_SESSION['user']) ? getCartQuantitiesByUser($_SESSION['user']['usId'], $pdo) : [];

$produits = getProduitsEnPromotion($pdo);
$services = array_values(array_filter(getServicesEnPromotion($pdo), function ($s) {
    return (bool) $s['available'];
}));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions - YosiShop</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
</head>

<body class="client">
    <?php include("./includes/header.php"); ?>

    <section class="container my-5 below-header">
        <h2 class="mb-1 fs-3">Promotions</h2>
        <p class="text-muted mb-4">Nos produits et services actuellement en promotion.</p>

        <?php if (empty($produits) && empty($services)): ?>
            <p class="text-muted">Aucune promotion en cours pour le moment.</p>
        <?php endif; ?>

        <?php if (!empty($produits)): ?>
            <h5 class="mb-3">Produits</h5>
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3 mb-5">
                <?php foreach ($produits as $produit): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm produit-card d-flex flex-column">
                            <?php $qte = $quantitesPanier['produit_' . $produit['proId']] ?? 0; ?>
                            <span class="panier-badge-carte <?= $qte > 0 ? '' : 'd-none' ?>" data-panier-key="produit_<?= $produit['proId'] ?>">×<?= $qte ?></span>

                            <a href="./products/detail.php?id=<?= $produit['proId'] ?>" class="text-decoration-none text-dark">
                                <?php if (!empty($produit['image'])): ?>
                                    <img src="./uploads/products/<?= htmlspecialchars($produit['image']) ?>"
                                        class="card-img-top produit-image"
                                        alt="<?= htmlspecialchars($produit['proName']) ?>">
                                <?php endif; ?>

                                <div class="card-body pb-0">
                                    <h6 class="card-title mb-1"><?= htmlspecialchars($produit['proName']) ?></h6>
                                    <p class="card-text text-muted small mb-0 produit-description">
                                        <?= htmlspecialchars($produit['prodescription'] ?? '') ?>
                                    </p>
                                </div>
                            </a>

                            <div class="card-body pt-2 mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <span class="text-muted text-decoration-line-through small">
                                            <?= number_format($produit['price'], 0, ',', ' ') ?> FCFA
                                        </span>
                                        <span class="fw-bold text-danger d-block">
                                            <?= number_format($produit['prixPromo'], 0, ',', ' ') ?> FCFA
                                        </span>
                                    </span>
                                    <form action="./cart/add.php" method="POST" class="m-0 ajout-panier">
                                        <input type="hidden" name="proId" value="<?= $produit['proId'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning" aria-label="Ajouter au panier" title="Ajouter au panier">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-basket2" viewBox="0 0 16 16">
                                                <path d="M4 10a1 1 0 0 1 2 0v2a1 1 0 0 1-2 0zm3 0a1 1 0 0 1 2 0v2a1 1 0 0 1-2 0zm3 0a1 1 0 1 1 2 0v2a1 1 0 0 1-2 0z" />
                                                <path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-.623l-1.844 6.456a.75.75 0 0 1-.722.544H3.69a.75.75 0 0 1-.722-.544L1.123 8H.5a.5.5 0 0 1-.5-.5v-1A.5.5 0 0 1 .5 6h1.717L5.07 1.243a.5.5 0 0 1 .686-.172zM2.163 8l1.714 6h8.246l1.714-6z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($services)): ?>
            <h5 class="mb-3">Services</h5>
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
                <?php foreach ($services as $service): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm produit-card service-card d-flex flex-column">
                            <?php $qte = $quantitesPanier['service_' . $service['servId']] ?? 0; ?>
                            <span class="panier-badge-carte <?= $qte > 0 ? '' : 'd-none' ?>" data-panier-key="service_<?= $service['servId'] ?>">×<?= $qte ?></span>

                            <a href="./services/detail.php?id=<?= $service['servId'] ?>" class="text-decoration-none text-dark">
                                <?php if (!empty($service['serImage'])): ?>
                                    <img src="./uploads/services/<?= htmlspecialchars($service['serImage']) ?>"
                                        class="card-img-top produit-image"
                                        alt="<?= htmlspecialchars($service['servName']) ?>">
                                <?php endif; ?>

                                <div class="card-body pb-0">
                                    <h6 class="card-title mb-1"><?= htmlspecialchars($service['servName']) ?></h6>
                                    <p class="card-text text-muted small mb-0 produit-description">
                                        <?= htmlspecialchars($service['description'] ?? '') ?>
                                    </p>
                                </div>
                            </a>

                            <div class="card-body pt-2 mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <span class="text-muted text-decoration-line-through small">
                                            <?= number_format($service['priceHours'], 0, ',', ' ') ?> FCFA/<?= htmlspecialchars($service['unite'] ?? 'heure') ?>
                                        </span>
                                        <span class="fw-bold text-danger d-block">
                                            <?= number_format($service['prixPromo'], 0, ',', ' ') ?> FCFA/<?= htmlspecialchars($service['unite'] ?? 'heure') ?>
                                        </span>
                                    </span>
                                    <form action="./cart/add.php" method="POST" class="m-0 ajout-panier">
                                        <input type="hidden" name="servId" value="<?= $service['servId'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning" aria-label="Ajouter au panier" title="Ajouter au panier">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-basket2" viewBox="0 0 16 16">
                                                <path d="M4 10a1 1 0 0 1 2 0v2a1 1 0 0 1-2 0zm3 0a1 1 0 0 1 2 0v2a1 1 0 0 1-2 0zm3 0a1 1 0 1 1 2 0v2a1 1 0 0 1-2 0z" />
                                                <path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-.623l-1.844 6.456a.75.75 0 0 1-.722.544H3.69a.75.75 0 0 1-.722-.544L1.123 8H.5a.5.5 0 0 1-.5-.5v-1A.5.5 0 0 1 .5 6h1.717L5.07 1.243a.5.5 0 0 1 .686-.172zM2.163 8l1.714 6h8.246l1.714-6z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php include("./includes/footer.php"); ?>

    <script src="./assets/js/bootstrap.js"></script>
    <script src="./assets/js/script.js?v=<?php echo filemtime(__DIR__ . '/assets/js/script.js'); ?>" defer></script>
</body>

</html>
