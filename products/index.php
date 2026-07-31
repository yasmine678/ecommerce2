<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../categories/controller.php");
require_once(__DIR__ . "/../auth/controller.php");

// 1. Vérification du rôle Administrateur
$isAdmin = isset($_SESSION['user']['role']) && (strtolower(trim($_SESSION['user']['role'])) === 'manager');

// 2. Gestion des données et du filtre par catégorie
$categories = getAll($pdo);
$categoryId = $_GET['category_id'] ?? $_GET['catId'] ?? null;

if ($categoryId) {
    $stmt = $pdo->prepare("SELECT p.*, c.name AS catName 
                           FROM product p 
                           LEFT JOIN category c ON p.catId = c.catId 
                           WHERE p.catId = ? 
                           ORDER BY p.proId DESC");
    $stmt->execute([$categoryId]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = getAllPro($pdo);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isAdmin ? "Gestion des Produits" : "Nos Produits" ?> - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/bootstrap.js" defer></script>
</head>

<body class="<?= $isAdmin ? 'bg-light' : 'client' ?>" style="<?= !$isAdmin ? 'background-color: #f8f9fa;' : '' ?>">

    <?php if ($isAdmin): ?>
        <!-- Barre latérale d'administration -->
        <?php include(__DIR__ . "/../admin/sidebar_admin.php"); ?>
    <?php else: ?>
        <!-- En-tête public / client -->
        <?php include(__DIR__ . "/../includes/header.php"); ?>
    <?php endif; ?>

    <main style="<?= $isAdmin ? 'margin-left: 250px;' : '' ?>" class="p-4<?= $isAdmin ? '' : ' below-header' ?>">
        <div class="container<?= $isAdmin ? '-fluid' : '' ?>">

            <!-- En-tête de page conditionnel -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <?= $isAdmin ? "Gestion des Produits" : "Nos Produits" ?>
                    </h2>
                    <p class="text-muted mb-0">
                        <?= $isAdmin ? "Consultez, ajoutez ou modifiez vos articles en stock." : "Découvrez nos articles disponibles dans la boutique." ?>
                    </p>
                </div>

                <?php if ($isAdmin): ?>
                    <!-- Bouton Ajouter (Visible uniquement par l'Admin) -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        + Ajouter
                    </button>

                    <!-- Ajout de produit -->
                    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="./save.php" method="POST" enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="addModalLabel">Ajouter un produit</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nom du produit</label>
                                            <input type="text" name="proName" id="name" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="desc" class="form-label">Description</label>
                                            <textarea name="prodescription" id="desc" class="form-control" rows="3"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Prix (FCFA)</label>
                                            <input type="number" name="price" id="price" step="0.01" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="catId" class="form-label">Catégorie</label>
                                            <select name="catId" id="catId" class="form-select" required>
                                                <option value="">-- Sélectionner une catégorie --</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category['catId'] ?>">
                                                        <?= htmlspecialchars($category['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Image du produit</label>
                                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" name="validate" value="Creer" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Messages d'alerte des opérations -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Affichage de la liste des produits -->
            <?php if (empty($products)): ?>
                <div class="alert alert-info text-center py-4">
                    <span>Aucun produit trouvé.</span>
                </div>
            <?php else: ?>

                <?php if ($isAdmin): ?>
                    <!-- VUE ADMINISTRATEUR (Lignes synthétiques) -->
                    <div class="row g-3">
                        <?php foreach ($products as $product): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm p-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                                       
                                        <span class="d-inline-flex align-items-center justify-content-center">
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" alt="Image produit" class="rounded border" width="50" height="50" style="object-fit: cover;">
                                            <?php else: ?>
                                                <span class="badge bg-secondary text-white p-3 rounded border">No Img</span>
                                            <?php endif; ?>
                                        </span>

                                     
                                        <span class="badge bg-dark text-white px-3 py-2 fs-6">
                                            <?= htmlspecialchars($product['proName'] ?? '') ?>
                                        </span>

                                     
                                        <span class="badge bg-light text-secondary border px-3 py-2 text-truncate" style="max-width: 250px;">
                                            <?= htmlspecialchars($product['prodescription'] ?? 'Pas de description') ?>
                                        </span>

                                
                                        <span class="badge bg-info text-dark bg-opacity-25 px-3 py-2 border border-info border-opacity-25 rounded-pill">
                                            <?= htmlspecialchars($product['name'] ?? 'Sans catégorie') ?>
                                        </span>

                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 fs-6">
                                            <?= number_format($product['price'], 0, ',', ' ') ?> FCFA
                                        </span>

                                        <!-- Actions Admin -->
                                        <span class="d-inline-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changeModal<?= $product['proId'] ?>">
                                                Modifier
                                            </button>

                                            <form action="./save.php" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce produit ?');">
                                                <input type="hidden" name="proId" value="<?= $product['proId'] ?>">
                                                <button type="submit" name="validate" value="Supprimer" class="btn btn-sm btn-outline-danger">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </span>

                                    </div>
                                </div>
                            </div>

                            <!-- Modification -->
                            <div class="modal fade" id="changeModal<?= $product['proId'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="./save.php" method="POST" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">Mise à jour du produit</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="proId" value="<?= $product['proId']; ?>">

                                                <div class="mb-3">
                                                    <label class="form-label">Nom du produit :</label>
                                                    <input type="text" name="proName" class="form-control" value="<?= htmlspecialchars($product['proName'] ?? ''); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description :</label>
                                                    <textarea name="prodescription" class="form-control" rows="3"><?= htmlspecialchars($product['prodescription'] ?? ''); ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Prix (FCFA) :</label>
                                                    <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($product['price'] ?? 0); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catégorie :</label>
                                                    <select name="catId" class="form-select" required>
                                                        <?php foreach ($categories as $category): ?>
                                                            <option value="<?= $category['catId']; ?>" <?= ($category['catId'] == $product['catId']) ? 'selected' : ''; ?>>
                                                                <?= htmlspecialchars($category['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label d-block mt-2">Changer l'image :</label>
                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" name="validate" value="mise à jour" class="btn btn-primary">Effectuer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php else: ?>

                    <!-- VUE CLIENT -->
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        <?php foreach ($products as $product): ?>
                            <div class="col">
                                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden d-flex flex-column">

                                    <div class="produit-clickable" role="button" tabindex="0"
                                        data-proid="<?= $product['proId'] ?>"
                                        data-name="<?= htmlspecialchars($product['proName'] ?? $product['name'] ?? '', ENT_QUOTES) ?>"
                                        data-desc="<?= htmlspecialchars($product['prodescription'] ?? 'Aucune description disponible.', ENT_QUOTES) ?>"
                                        data-price="<?= number_format($product['price'] ?? 0, 0, ',', ' ') ?>"
                                        data-image="<?= !empty($product['image']) ? '../assets/images/' . htmlspecialchars($product['image'], ENT_QUOTES) : '' ?>"
                                        data-cat="<?= htmlspecialchars($product['catName'] ?? $product['name'] ?? '', ENT_QUOTES) ?>">
                                        <!-- Image produit ou placeholder avec hauteur fixe -->
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px; width: 100%;">
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>"
                                                    class="card-img-top w-100 h-100 "
                                                    alt="<?= htmlspecialchars($product['proName'] ?? 'Produit') ?>"
                                                    style="object-fit: cover;">
                                            <?php else: ?>
                                                <div class="text-center text-muted">
                                                    <span class="fs-1">A</span>
                                                    <p class="small mb-0">Pas d'image</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="card-body pb-0">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="fw-bold text-dark mb-0">
                                                    <?= htmlspecialchars($product['proName'] ?? $product['name'] ?? '') ?>
                                                </h5>
                                                <span class="badge bg-info text-dark bg-opacity-25 border border-info border-opacity-25 rounded-pill">
                                                    <?= htmlspecialchars($product['catName'] ?? $product['name'] ?? 'Général') ?>
                                                </span>
                                            </div>
                                            <p class="text-muted small mb-0">
                                                <?= htmlspecialchars($product['prodescription'] ?? 'Aucune description disponible.') ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="card-body pt-2 mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-warning fs-5">
                                                <?= number_format($product['price'] ?? 0, 0, ',', ' ') ?> FCFA
                                            </span>
                                            <form action="../cart/add.php" method="POST" class="m-0 ajout-panier">
                                                <input type="hidden" name="proId" value="<?php echo $product['proId']; ?>">
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
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </main>

    <?php if (!$isAdmin): ?>
        <?php include(__DIR__ . "/../includes/produit-modal.php"); ?>
        <script src="../assets/js/script.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/script.js'); ?>" defer></script>
    <?php endif; ?>

</body>

</html>