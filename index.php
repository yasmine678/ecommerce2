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
            
            <!-- Zone des Catégories en mode défilement horizontal (Carrousel fluide) -->
            <div class="col-12 col-md-7 col-lg-8">
                <div class="scroll-categories gap-2">
                    <?php foreach ($categories as $cat): ?>
                        <a href="./categories/produits.php?catId=<?php echo $cat['catId']; ?>"
                            class="btn btn-outline-secondary rounded-pill px-3 d-inline-block">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Barre de recherche alignée sur la même ligne -->
            <div class="col-12 col-md-5 col-lg-4">
                <form action="./recherche.php" method="GET" class="d-flex">
                    <input type="search" name="q" class="form-control me-2" placeholder="Rechercher un produit..." required>
                    <button class="btn btn-warning" type="submit">🔍</button>
                </form>
            </div>

        </div>
    </section>

    <!-- PRODUITS + PAGINATION -->
    <section class="container my-5">
        <h2 class="mb-4 fs-3">Nos produits</h2>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
            <?php foreach ($produitsAffiches as $produit): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm produit-card">
                        <!-- Correction de la source de l'image (Vérifiez bien ce dossier) -->
                        <img src="./assets/images/produits/<?php echo htmlspecialchars($produit['image']); ?>"
                            class="card-img-top produit-image" alt="<?php echo htmlspecialchars($produit['proName']); ?>">

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-1"><?php echo htmlspecialchars($produit['proName']); ?></h6>
                            <p class="card-text text-muted small mb-2 produit-description">
                                <?php echo htmlspecialchars($produit['prodescription']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="fw-bold text-warning">
                                    <?php echo number_format($produit['price'], 0, ',', ' '); ?> FCFA
                                </span>
                                <form action="./traitement_ajout_panier.php" method="POST" class="m-0">
                                    <input type="hidden" name="produit_id" value="<?php echo $produit['proId']; ?>">
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
                        <?php elseif (($i === 3 && $page > 4) || ($i === $totalPages - 1 && $page < $totalPages - 3)): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php $i = ($i === 3) ? $page - 2 : $totalPages - 2; ?>
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
    </section>

    <?php include("./includes/footer.php"); ?>
    <script src="/assets/js/bootstrap%20.js"></script>
</body>

</html>