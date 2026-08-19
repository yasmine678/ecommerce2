<?php
session_start();

require_once(__DIR__ . "/products/controller.php");
require_once(__DIR__ . "/services/controller.php");
require_once(__DIR__ . "/config/db.php");
require_once(__DIR__ . "/cart/controller.php");

$quantitesPanier = isset($_SESSION['user']) ? getCartQuantitiesByUser($_SESSION['user']['usId'], $pdo) : [];

$joursNouveaute = 14;

$produits = getProduitsRecents($pdo, $joursNouveaute);
$produitsParPage = 8;
$pagesProduits = array_chunk($produits, $produitsParPage);
$totalPages = count($pagesProduits);
$page = isset($_GET['page']) ? max(1, min($totalPages, (int) $_GET['page'])) : 1;
$produitsAffiches = $pagesProduits[$page - 1] ?? [];

$services = array_values(array_filter(getServicesRecents($pdo, $joursNouveaute), function ($s) {
    return (bool) $s['available'];
}));
$servicesParPage = 8;
$pagesServices = array_chunk($services, $servicesParPage);
$totalPagesServices = count($pagesServices);
$pageServices = isset($_GET['page_services']) ? max(1, min($totalPagesServices, (int) $_GET['page_services'])) : 1;
$servicesAffiches = $pagesServices[$pageServices - 1] ?? [];

function urlAvecPages(int $page, int $pageServices): string
{
    return '?page=' . $page . '&page_services=' . $pageServices;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveautés - YosiShop</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
</head>

<body class="client">
    <?php include("./includes/header.php"); ?>

    <section class="container my-5 below-header">
        <h2 class="mb-1 fs-3">Nouveautés</h2>
        <p class="text-muted mb-4">Produits et services ajoutés au cours des <?php echo $joursNouveaute; ?> derniers jours.</p>

        <?php if (empty($produitsAffiches)): ?>
            <p class="text-muted">Aucun nouveau produit pour le moment.</p>
        <?php else: ?>
            <h5 class="mb-3">Produits</h5>
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
                <?php foreach ($produitsAffiches as $produit): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm produit-card d-flex flex-column">
                            <?php $qte = $quantitesPanier['produit_' . $produit['proId']] ?? 0; ?>
                            <span class="panier-badge-carte <?= $qte > 0 ? '' : 'd-none' ?>" data-panier-key="produit_<?= $produit['proId'] ?>">×<?= $qte ?></span>
                            <a href="./products/detail.php?id=<?php echo $produit['proId']; ?>" class="text-decoration-none text-dark">
                                <img src="./uploads/products/<?php echo htmlspecialchars($produit['image']); ?>"
                                    class="card-img-top produit-image"
                                    alt="<?php echo htmlspecialchars($produit['proName']); ?>">

                                <div class="card-body pb-0">
                                    <h6 class="card-title mb-1"><?php echo htmlspecialchars($produit['proName']); ?></h6>
                                    <p class="card-text text-muted small mb-0 produit-description">
                                        <?php echo htmlspecialchars($produit['prodescription']); ?>
                                    </p>
                                </div>
                            </a>

                            <div class="card-body pt-2 mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-warning">
                                        <?php echo number_format($produit['price'], 0, ',', ' '); ?> FCFA
                                    </span>
                                    <form action="./cart/add.php" method="POST" class="m-0 ajout-panier">
                                        <input type="hidden" name="proId" value="<?php echo $produit['proId']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-basket2" viewBox="0 0 16 16">
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

            <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php $afficherNumero = ($i <= 2) || ($i > $totalPages - 1) || abs($i - $page) <= 1; ?>
                            <?php if ($afficherNumero): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo urlAvecPages($i, $pageServices); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php elseif ($i === 3 && $page > 4): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo urlAvecPages($page + 1, $pageServices); ?>">&gt;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <section class="container my-5">
        <?php if (empty($servicesAffiches)): ?>
            <h5 class="mb-3">Services</h5>
            <p class="text-muted">Aucun nouveau service pour le moment.</p>
        <?php else: ?>
            <h5 class="mb-3">Services</h5>
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
                <?php foreach ($servicesAffiches as $service): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm produit-card service-card d-flex flex-column">
                            <?php $qte = $quantitesPanier['service_' . $service['servId']] ?? 0; ?>
                            <span class="panier-badge-carte <?= $qte > 0 ? '' : 'd-none' ?>" data-panier-key="service_<?= $service['servId'] ?>">×<?= $qte ?></span>
                            <a href="./services/detail.php?id=<?php echo $service['servId']; ?>" class="text-decoration-none text-dark">
                                <?php if (!empty($service['serImage'])): ?>
                                    <img src="./uploads/services/<?php echo htmlspecialchars($service['serImage']); ?>"
                                        class="card-img-top produit-image"
                                        alt="<?php echo htmlspecialchars($service['servName']); ?>">
                                <?php endif; ?>

                                <div class="card-body pb-0">
                                    <h6 class="card-title mb-1"><?php echo htmlspecialchars($service['servName']); ?></h6>
                                    <p class="card-text text-muted small mb-0 produit-description">
                                        <?php echo htmlspecialchars($service['description'] ?? ''); ?>
                                    </p>
                                </div>
                            </a>

                            <div class="card-body pt-2 mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-warning">
                                        <?php echo number_format($service['priceHours'], 0, ',', ' '); ?> FCFA/<?php echo htmlspecialchars($service['unite'] ?? 'heure'); ?>
                                    </span>
                                    <form action="./cart/add.php" method="POST" class="m-0 ajout-panier">
                                        <input type="hidden" name="servId" value="<?php echo $service['servId']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-basket2" viewBox="0 0 16 16">
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

            <?php if ($totalPagesServices > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPagesServices; $i++): ?>
                            <?php $afficherNumero = ($i <= 2) || ($i > $totalPagesServices - 1) || abs($i - $pageServices) <= 1; ?>
                            <?php if ($afficherNumero): ?>
                                <li class="page-item <?php echo $i === $pageServices ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo urlAvecPages($page, $i); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php elseif ($i === 3 && $pageServices > 4): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($pageServices < $totalPagesServices): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo urlAvecPages($page, $pageServices + 1); ?>">&gt;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <?php include("./includes/footer.php"); ?>

    <script src="./assets/js/bootstrap.js"></script>
    <script src="./assets/js/script.js?v=<?php echo filemtime(__DIR__ . '/assets/js/script.js'); ?>" defer></script>
</body>

</html>
