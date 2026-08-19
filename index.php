<?php
session_start();

require_once(__DIR__ . "/products/controller.php");
require_once(__DIR__ . "/config/db.php");
require_once(__DIR__ . "/categories/controller.php");
require_once(__DIR__ . "/services/controller.php");
require_once(__DIR__ . "/cart/controller.php");

$quantitesPanier = isset($_SESSION['user']) ? getCartQuantitiesByUser($_SESSION['user']['usId'], $pdo) : [];

$categories = getAll($pdo);
$produits = getAllPro($pdo);

$produitsParPage = 8;
$pagesProduits = array_chunk($produits, $produitsParPage);
$totalPages = count($pagesProduits);
$page = isset($_GET['page']) ? max(1, min($totalPages, (int) $_GET['page'])) : 1;
$produitsAffiches = $pagesProduits[$page - 1] ?? [];

$services = array_values(array_filter(getAllServices($pdo), function ($s) {
    return (bool) $s['available'];
}));

$servicesParPage = 4;
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
    <title>YosiDShop</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
</head>

<body class="client">
    <?php include("./includes/header.php"); ?>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-content">
            <span class="hero-badge">Nouveautés chaque semaine</span>
            <h1>Votre boutique <span>tout-en-un</span><br>pour acheter mieux</h1>
            <p>Découvrez des produits et services choisis avec soin,<br>à des prix qui font plaisir.</p>
            <div>
                <a href="./promotions.php" class="btn btn-hero-primary btn-lg">Promotion</a>
                <a href="./nouveautes.php" class="btn btn-hero-ghost btn-lg">Nouveautés</a>
            </div>
        </div>
    </section>


    <!-- BARRE UNIQUE : CATEGORIES DÉFILANTES + RECHERCHE -->
    <section id="categories" class="container my-4 reveal">
        <div class="row g-3 align-items-center">

            <!-- Zone des Catégories en mode défilement horizontal -->
            <div class="col-12 col-md-7 col-lg-8 d-flex align-items-center gap-2">
                <button id="gauche" class="btn btn-light flex-shrink-0" aria-label="Précédent" title="Précédent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
                    </svg>
                </button>

                <div id="listeCategories" class="scroll-categories gap-2">

                    <?php foreach ($categories as $cat): ?>

                        <button type="button"
                            class="btn btn-outline-secondary rounded-pill px-3 d-inline-block categorie-trigger"
                            data-catid="<?php echo $cat['catId']; ?>"
                            data-catname="<?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </button>
                    <?php endforeach; ?>

                </div>

                <button id="droite" class="btn btn-light flex-shrink-0" aria-label="Suivant" title="Suivant">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z" />
                    </svg>
                </button>
            </div>

            <!-- Barre de recherche alignée sur la même ligne -->
            <div class="col-12 col-md-5 col-lg-4">
                <form action="./researsh.php" method="GET" class="d-flex recherche-form">
                    <input type="search" name="q" class="form-control me-2" placeholder="Rechercher un produit ou un service..." required>
                    <button class="btn btn-warning" type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg></button>
                </form>
            </div>

        </div>
    </section>

    <!-- PRODUITS + PAGINATION -->
    <section id="produits" class="container my-5 reveal">
        <h2 class="section-title">Nos produits</h2>

        <?php if (empty($produitsAffiches)): ?>
            <p class="text-muted">Aucun produit pour le moment.</p>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3 cartes-animees">
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

            <!-- Pagination -->
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

    <!-- ATOUTS -->
    <section class="hero-features">
        <div class="container">
            <div class="row row-cols-1 row-cols-sm-3 g-3">
                <div class="col">
                    <div class="feature-item">
                        <span class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12z"/></svg>
                        </span>
                        <span class="feature-text">
                            <strong>Livraison rapide</strong>
                            <span>Partout dans la ville</span>
                        </span>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-item">
                        <span class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/></svg>
                        </span>
                        <span class="feature-text">
                            <strong>Paiement sécurisé</strong>
                            <span>Vos données protégées</span>
                        </span>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-item">
                        <span class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/></svg>
                        </span>
                        <span class="feature-text">
                            <strong>Qualité garantie</strong>
                            <span>Produits vérifiés</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES + PAGINATION -->
    <section id="services" class="container my-5 reveal">
        <h2 class="section-title text-center">Nos services</h2>

        <?php if (empty($servicesAffiches)): ?>
            <p class="text-muted">Aucun service disponible pour le moment.</p>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3 cartes-animees">
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

            <!-- Pagination -->
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

    <!-- Modal catégorie : 4 produits de la catégorie cliquée, rempli en JS -->
    <div class="modal fade" id="categorieModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categorieModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="categorieModalBody" class="row row-cols-2 row-cols-md-4 g-3"></div>
                </div>
                <div class="modal-footer">
                    <a id="categorieModalVoirPlus" href="#" class="btn btn-warning">Voir tous les produits</a>
                    <a id="categorieModalVoirPlusServices" href="#" class="btn btn-outline-warning">Voir tous les services</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal recherche : résultats affichés sans quitter la page, rempli en JS -->
    <div class="modal fade" id="rechercheModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rechercheModalTitle">Résultats de recherche</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="rechercheModalBody" class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/bootstrap.js"></script>
    <script src="./assets/js/script.js?v=<?php echo filemtime(__DIR__ . '/assets/js/script.js'); ?>"></script>
</body>

</html>