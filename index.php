<?php
session_start();

require_once(__DIR__ . "/products/controller.php");
require_once(__DIR__ . "/config/db.php");
require_once(__DIR__ . "/categories/controller.php");

$categories = getAll($pdo);
$produits = getAllPro($pdo);

$produitsParPage = 15;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$totalProduits = count($produits);
$totalPages = (int) ceil($totalProduits / $produitsParPage);
$debut = ($page - 1) * $produitsParPage;
$produitsAffiches = array_slice($produits, $debut, $produitsParPage);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YosiShop</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
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
            <div class="col-12 col-md-7 col-lg-8">
                <div class="scroll-categories gap-2">
                    <?php foreach ($categories as $cat): ?>
                        <a href="./products/index.php?catId=<?php echo $cat['catId']; ?>"
                            class="btn btn-outline-secondary rounded-pill px-3 d-inline-block">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Barre de recherche alignée sur la même ligne -->
            <div class="col-12 col-md-5 col-lg-4">
                <form action="./researsh.php" method="GET" class="d-flex">
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
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                <?php foreach ($produitsAffiches as $produit): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm produit-card">
                            <img src="./assets/images/<?php echo htmlspecialchars($produit['image']); ?>"
                                class="card-img-top produit-image"
                                alt="<?php echo htmlspecialchars($produit['proName']); ?>">

                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-1"><?php echo htmlspecialchars($produit['proName']); ?></h6>
                                <p class="card-text text-muted small mb-2 produit-description">
                                    <?php echo htmlspecialchars($produit['prodescription']); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="fw-bold text-warning">
                                        <?php echo number_format($produit['price'], 0, ',', ' '); ?> FCFA
                                    </span>
                                    <form action="./cart/add.php" method="POST" class="m-0">
                                        <input type="hidden" name="proId" value="<?php echo $produit['proId']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning">🛒</button>
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
    <script src="/ecommerce/assets/js/bootstrap.js"></script>
</body>

</html>