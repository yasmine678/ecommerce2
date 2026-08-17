<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../cart/controller.php");

$proId = (int) ($_GET['id'] ?? 0);
$produit = $proId ? getProduitAvecCategorie($proId, $pdo) : null;

if (!$produit) {
    header("Location: ./index.php");
    exit();
}

$variantes = getVariantesProduit($produit['proName'], $proId, $pdo);

$quantitesPanier = isset($_SESSION['user']) ? getCartQuantitiesByUser($_SESSION['user']['usId'], $pdo) : [];
$qte = $quantitesPanier['produit_' . $proId] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($produit['proName']) ?> - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>
<body class="client">
    <?php include(__DIR__ . "/../includes/header.php"); ?>

    <div class="container my-5 below-header">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Accueil</a></li>
                <li class="breadcrumb-item"><a href="./index.php" class="text-decoration-none">Produits</a></li>
                <?php if (!empty($produit['catName'])): ?>
                    <li class="breadcrumb-item">
                        <a href="./index.php?catId=<?= $produit['catId'] ?>" class="text-decoration-none"><?= htmlspecialchars($produit['catName']) ?></a>
                    </li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($produit['proName']) ?></li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-md-6">
                <?php if (!empty($produit['image'])): ?>
                    <img src="../uploads/products/<?= htmlspecialchars($produit['image']) ?>"
                         class="img-fluid rounded-3 border w-100" style="object-fit: cover; max-height: 480px;"
                         alt="<?= htmlspecialchars($produit['proName']) ?>">
                <?php else: ?>
                    <div class="bg-light rounded-3 border d-flex align-items-center justify-content-center" style="height: 400px;">
                        <span class="text-muted">Pas d'image</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <?php if (!empty($produit['catName'])): ?>
                    <span class="badge bg-info text-dark bg-opacity-25 mb-2"><?= htmlspecialchars($produit['catName']) ?></span>
                <?php endif; ?>

                <h1 class="h3 fw-bold mb-3"><?= htmlspecialchars($produit['proName']) ?></h1>

                <?php if (!empty($produit['color']) || !empty($produit['capacite'])): ?>
                    <p class="text-muted mb-2">
                        <?php if (!empty($produit['color'])): ?>Couleur : <strong><?= htmlspecialchars($produit['color']) ?></strong><?php endif; ?>
                        <?php if (!empty($produit['color']) && !empty($produit['capacite'])): ?> · <?php endif; ?>
                        <?php if (!empty($produit['capacite'])): ?>Capacité : <strong><?= htmlspecialchars($produit['capacite']) ?></strong><?php endif; ?>
                    </p>
                <?php endif; ?>

                <p class="fs-3 fw-bold text-warning mb-3"><?= number_format($produit['price'], 0, ',', ' ') ?> FCFA</p>

                <p class="text-muted"><?= nl2br(htmlspecialchars($produit['prodescription'] ?? 'Aucune description disponible.')) ?></p>

                <form action="../cart/add.php" method="POST" class="ajout-panier d-flex align-items-center gap-3 mt-4">
                    <input type="hidden" name="proId" value="<?= $proId ?>">
                    <input type="number" name="cquantity" value="1" min="1" class="form-control" style="width: 90px;">
                    <button type="submit" class="btn btn-warning btn-lg d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-basket2" viewBox="0 0 16 16">
                            <path d="M4 10a1 1 0 0 1 2 0v2a1 1 0 0 1-2 0zm3 0a1 1 0 0 1 2 0v2a1 1 0 0 1-2 0zm3 0a1 1 0 1 1 2 0v2a1 1 0 0 1-2 0z" />
                            <path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-.623l-1.844 6.456a.75.75 0 0 1-.722.544H3.69a.75.75 0 0 1-.722-.544L1.123 8H.5a.5.5 0 0 1-.5-.5v-1A.5.5 0 0 1 .5 6h1.717L5.07 1.243a.5.5 0 0 1 .686-.172zM2.163 8l1.714 6h8.246l1.714-6z" />
                        </svg>
                        Ajouter au panier
                    </button>
                    <?php if ($qte > 0): ?>
                        <span class="badge bg-dark">Déjà ×<?= $qte ?> dans votre panier</span>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if (!empty($variantes)): ?>
            <hr class="my-5">
            <h5 class="mb-3">Autres exemplaires de ce produit</h5>
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
                <?php foreach ($variantes as $variante): ?>
                    <div class="col">
                        <a href="./detail.php?id=<?= $variante['proId'] ?>" class="text-decoration-none text-dark">
                            <div class="card h-100 shadow-sm produit-card">
                                <?php if (!empty($variante['image'])): ?>
                                    <img src="../uploads/products/<?= htmlspecialchars($variante['image']) ?>"
                                         class="card-img-top" style="height: 120px; object-fit: cover;"
                                         alt="<?= htmlspecialchars($variante['proName']) ?>">
                                <?php endif; ?>
                                <div class="card-body p-2 text-center">
                                    <?php if (!empty($variante['color'])): ?>
                                        <p class="mb-0 small fw-semibold"><?= htmlspecialchars($variante['color']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($variante['capacite'])): ?>
                                        <p class="mb-0 small text-muted"><?= htmlspecialchars($variante['capacite']) ?></p>
                                    <?php endif; ?>
                                    <?php if (empty($variante['color']) && empty($variante['capacite'])): ?>
                                        <p class="mb-0 small text-muted">Voir</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

    <?php include(__DIR__ . "/../includes/footer.php"); ?>
    <script src="../assets/js/bootstrap.js"></script>
    <script src="../assets/js/script.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/script.js'); ?>" defer></script>
</body>
</html>
