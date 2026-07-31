<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/products/controller.php");
require_once(__DIR__ . "/config/db.php");

$motCle = trim($_GET['q'] ?? '');
function searchProducts(string $keyword, PDO $pdo)
{
    $req = "SELECT product.*, category.name AS catName
            FROM product
            LEFT JOIN category ON product.catId = category.catId
            WHERE product.proName LIKE :keyword
               OR product.prodescription LIKE :keyword
               OR category.name LIKE :keyword
            ORDER BY product.proName ASC";

    $stmt = $pdo->prepare($req);
    $stmt->execute([':keyword' => '%' . $keyword . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$resultats = [];

if ($motCle !== '') {
    $resultats = searchProducts($motCle, $pdo);
}

if (($_GET['format'] ?? '') === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'motCle' => $motCle,
        'total' => count($resultats),
        'produits' => $resultats,
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche - YosiShop</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body class="client">
    <?php include(__DIR__ . "/includes/header.php"); ?>

    <div class="container my-5 below-header">

        <!-- Barre de recherche, reaffichee ici pour permettre une nouvelle recherche -->
        <form action="./researsh.php" method="GET" class="d-flex mb-4 recherche-form" style="max-width: 500px;">
            <input type="search" name="q" class="form-control me-2"
                   placeholder="Rechercher un produit..."
                   value="<?= htmlspecialchars($motCle) ?>" required>
            <button class="btn btn-warning" type="submit">Rechercher</button>
        </form>

        <?php if ($motCle === ''): ?>
            <p class="text-muted">Saisissez un mot-clé pour lancer une recherche.</p>

        <?php elseif (empty($resultats)): ?>
            <h2 class="mb-3 fs-4">Résultats pour « <?= htmlspecialchars($motCle) ?> »</h2>
            <div class="alert alert-info">
                Aucun produit ne correspond à votre recherche.
            </div>

        <?php else: ?>
            <h2 class="mb-4 fs-4">
                <?= count($resultats) ?> résultat<?= count($resultats) > 1 ? 's' : '' ?>
                pour « <?= htmlspecialchars($motCle) ?> »
            </h2>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                <?php foreach ($resultats as $produit): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm produit-card">
                            <?php if (!empty($produit['image'])): ?>
                                <a href="./products/detail.php?id=<?= $produit['proId'] ?>">
                                    <img src="./assets/images/<?= htmlspecialchars($produit['image']) ?>"
                                         class="card-img-top produit-image"
                                         alt="<?= htmlspecialchars($produit['proName']) ?>">
                                </a>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-1">
                                    <a href="./products/detail.php?id=<?= $produit['proId'] ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($produit['proName']) ?>
                                    </a>
                                </h6>
                                <p class="card-text text-muted small mb-1 produit-description">
                                    <?= htmlspecialchars($produit['prodescription'] ?? '') ?>
                                </p>
                                <span class="badge bg-info text-dark bg-opacity-25 align-self-start mb-2">
                                    <?= htmlspecialchars($produit['catName'] ?? 'Sans catégorie') ?>
                                </span>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="fw-bold text-warning">
                                        <?= number_format($produit['price'], 0, ',', ' ') ?> FCFA
                                    </span>
                                    <form action="./cart/add.php" method="POST" class="m-0 ajout-panier">
                                        <input type="hidden" name="proId" value="<?= $produit['proId'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning">🛒</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

   
    <script src="./assets/js/bootstrap.js"></script>
    <script src="./assets/js/script.js?v=<?php echo filemtime(__DIR__ . '/assets/js/script.js'); ?>"></script>
</body>
</html>
