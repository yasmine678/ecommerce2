<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../categories/controller.php");
require_once(__DIR__ . "/../auth/controller.php");

if (!isset($_SESSION['user'])) {
    header("Location: /ecommerce/auth/login.php");
    exit();
}

$products = getAllPro($pdo);
$categories = getAll($pdo);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/bootstrap.js" defer></script>
</head>

<body class="bg-light">

    <?php include(__DIR__ . "/../admin/sidebar_admin.php"); ?>

    <main style="margin-left: 250px;" class="p-4">

        <div class="container-fluid">


            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Gestion des Produits</h2>
                    <p class="text-muted mb-0">Consultez, ajoutez ou modifiez vos articles en stock.</p>
                </div>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    + Ajouter
                </button>


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
                                        <input type="text" name="proname" id="name" class="form-control" required>
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
            </div>

            <!-- Liste sous forme de cartes d'éléments composés exclusivement de spans -->
            <div class="row g-3">
                <?php if (empty($products)): ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center py-4">
                            <span>Aucun produit trouvé.</span>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm p-3">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                                    <!-- Image du Produit en span -->
                                    <span class="d-inline-flex align-items-center justify-content-center">
                                        <?php if (!empty($product['image']) && file_exists(__DIR__ . "/../assets/images/" . $product['image'])): ?>
                                            <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>"
                                                alt="Image produit"
                                                class="rounded border"
                                                width="50"
                                                height="50"
                                                style="object-fit: cover;">
                                        <?php else: ?>
                                            <span class="badge bg-secondary text-white p-3 rounded border">
                                                No Img
                                            </span>
                                        <?php endif; ?>
                                    </span>
                                    

                                    <span class="badge bg-dark text-white px-3 py-2 fs-6">
                                        <?= htmlspecialchars($product['proName']) ?>
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

                                    <!-- Actions (Boutons) -->
                                    <span class="d-inline-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changeModal<?= $product['proId'] ?>">
                                            Modifier
                                        </button>

                                        <form action="./save.php" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce produit ?');">
                                            <input type="hidden" name="proid" value="<?= $product['proId'] ?>">
                                            <button type="submit" name="validate" value="Supprimer" class="btn btn-sm btn-outline-danger">
                                                Supprimer
                                            </button>
                                        </form>
                                    </span>

                                </div>
                            </div>
                        </div>

                        <!-- Modal Modification -->
                        <div class="modal fade" id="changeModal<?= $product['proId'] ?>" tabindex="-1" aria-labelledby="changeModalLabel<?= $product['proId'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="./save.php" method="POST" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="changeModalLabel<?= $product['proId'] ?>">Mise à jour du produit</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="proid" value="<?= $product['proId']; ?>">

                                            <div class="mb-3">
                                                <label class="form-label">Nom du produit :</label>
                                                <input type="text" name="proname" class="form-control" value="<?= htmlspecialchars($product['proName']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description :</label>
                                                <textarea name="prodescription" class="form-control" rows="3"><?= htmlspecialchars($product['prodescription'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Prix (FCFA) :</label>
                                                <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($product['price']); ?>" required>
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
                                                <span class="form-label d-block mb-2">Image actuelle :</span>
                                                <?php if (!empty($product['image']) && file_exists(__DIR__ . "/../assets/images/" . $product['image'])): ?>
                                                    <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" alt="Aperçu" class="img-thumbnail mb-2" style="max-height: 80px;">
                                                <?php else: ?>
                                                    <span class="badge bg-secondary text-white d-inline-block mb-2">Aucune image enregistrée</span>
                                                <?php endif; ?>

                                                <label class="form-label d-block mt-2">Changer l'image :</label>
                                                <input type="file" name="image" class="form-control" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" name="validate" value="Mise à jour" class="btn btn-primary">Effectuer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </main>

</body>

</html>