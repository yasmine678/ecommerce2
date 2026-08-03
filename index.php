<?php
session_start();

require_once(__DIR__ . "/products/controller.php");
require_once(__DIR__ . "/config/db.php");
require_once(__DIR__ . "/categories/controller.php");

$categories = getAll($pdo);
$produits = getAllPro($pdo);

$produitsParPage = 10;
$pagesProduits = array_chunk($produits, $produitsParPage);
$totalPages = count($pagesProduits);
$page = isset($_GET['page']) ? max(1, min($totalPages, (int) $_GET['page'])) : 1;
$produitsAffiches = $pagesProduits[$page - 1] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YosiShop</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
</head>

<body class="client">
    <?php include("./includes/header.php"); ?>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-content">
            <h1>Bienvenue chez <span>YosiShop</span></h1>
            <p>Découvrez les meilleurs produits aux meilleurs prix.</p>
        </div>
    </section>

    <!-- BARRE UNIQUE : CATEGORIES DÉFILANTES + RECHERCHE -->
    <section class="container my-4">
        <div class="row g-3 align-items-center">

            <!-- Zone des Catégories en mode défilement horizontal -->
            <div class="col-12 col-md-7 col-lg-8 d-flex align-items-center gap-2">
                <button id="gauche" class="btn btn-light flex-shrink-0">❮</button>

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

                <button id="droite" class="btn btn-light flex-shrink-0">❯</button>
            </div>

            <!-- Barre de recherche alignée sur la même ligne -->
            <div class="col-12 col-md-5 col-lg-4">
                <form action="./researsh.php" method="GET" class="d-flex recherche-form">
                    <input type="search" name="q" class="form-control me-2" placeholder="Rechercher un produit..." required>
                    <button class="btn btn-warning" type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg></button>
                </form>
            </div>

        </div>
    </section>

    <!-- PRODUITS + PAGINATION -->
    <section class="container my-5">
        <h2 class="mb-4 fs-3">Nos produits</h2>

        <?php if (empty($produitsAffiches)): ?>
            <p class="text-muted">Aucun produit pour le moment.</p>
        <?php else: ?>
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-3">
                <?php foreach ($produitsAffiches as $produit): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm produit-card d-flex flex-column">
                            <div class="produit-clickable" role="button" tabindex="0"
                                data-proid="<?php echo $produit['proId']; ?>"
                                data-name="<?php echo htmlspecialchars($produit['proName'], ENT_QUOTES); ?>"
                                data-desc="<?php echo htmlspecialchars($produit['prodescription'], ENT_QUOTES); ?>"
                                data-price="<?php echo number_format($produit['price'], 0, ',', ' '); ?>"
                                data-image="./assets/images/<?php echo htmlspecialchars($produit['image'], ENT_QUOTES); ?>"
                                data-cat="<?php echo htmlspecialchars($produit['name'] ?? '', ENT_QUOTES); ?>">
                                <img src="./assets/images/<?php echo htmlspecialchars($produit['image']); ?>"
                                    class="card-img-top produit-image"
                                    alt="<?php echo htmlspecialchars($produit['proName']); ?>">

                                <div class="card-body pb-0">
                                    <h6 class="card-title mb-1"><?php echo htmlspecialchars($produit['proName']); ?></h6>
                                    <p class="card-text text-muted small mb-0 produit-description">
                                        <?php echo htmlspecialchars($produit['prodescription']); ?>
                                    </p>
                                </div>
                            </div>

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
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php elseif ($i === 3 && $page > 4): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>">&gt;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <?php include("./includes/footer.php"); ?>

    <!-- Modal détail produit : rempli dynamiquement en JS au clic sur une carte produit -->
    <div class="modal fade" id="produitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="produitModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="produitModalCat" class="badge bg-info text-dark bg-opacity-25 border border-info border-opacity-25 rounded-pill mb-2"></span>
                    <p id="produitModalDesc" class="text-muted"></p>
                    <p class="fw-bold text-warning fs-5" id="produitModalPrice"></p>
                </div>
            </div>
        </div>
    </div>

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
                    <a id="categorieModalVoirPlus" href="#" class="btn btn-warning">Voir plus</a>
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