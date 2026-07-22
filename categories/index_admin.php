<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/controller.php");

// 1. Récupération des catégories
$categories = getAll($pdo);

// 2. Vérification du rôle d'administration
$isAdmin = isset($_SESSION['user']) && ($_SESSION['user']['role'] === 'admin');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/bootstrap.js" defer></script>
</head>

<body class="bg-light" style="padding-top: 90px;">

    <!-- Sidebar Admin / En-tête -->
    <?php include(__DIR__ . "/../includes/sidebar_admin.php"); ?>

    <main class="container my-4">

        <!-- En-tête de page -->
        <div class="d-flex justify-content-between align-items-center mb-4" style="margin-left: 260px; width: calc(100% - 260px);">
            <div>
                <h2 class="fw-bold text-dark mb-0">
                    <?= $isAdmin ? "Gestion des catégories" : "Nos Catégories" ?>
                </h2>
            </div>

            <!-- Bouton déclencheur du modal d'ajout -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                + Ajouter
            </button>

            <!-- Modal Ajout -->
            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="./save.php" method="POST">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="addModalLabel">Ajouter une catégorie</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom de la catégorie</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="desc" class="form-label">Description</label>
                                    <textarea name="description" id="desc" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" name="validate" value="Creer" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des catégories -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3" style="margin-left: 260px; width: calc(100% - 260px);">
            <?php if (empty($categories)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center py-4">
                        <span>Aucune catégorie trouvée.</span>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 rounded-3">
                            <div class="card-body d-flex flex-column justify-content-between p-3">

                                <!-- Informations sur la catégorie -->
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">
                                        <?= htmlspecialchars($category['name'] ?? '') ?>
                                    </h5>
                                    <p class="text-muted small mb-0">
                                        <?= htmlspecialchars($category['description'] ?? 'Aucune description disponible.') ?>
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="mt-3 pt-2 border-top">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#changeModal<?= $category['catId'] ?>">
                                            Mise à jour
                                        </button>

                                        <!-- Formulaire Suppression -->
                                        <form action="./save.php" method="POST" class="m-0" onsubmit="return confirm('Voulez-vous vraiment supprimer cette catégorie ?');">
                                            <input type="hidden" name="id" value="<?= $category['catId'] ?>">
                                            <button type="submit" name="validate" value="Supprimer" class="btn btn-sm btn-outline-danger">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Modal Mise à jour (pour chaque catégorie) -->
                    <div class="modal fade" id="changeModal<?= $category['catId'] ?>" tabindex="-1" aria-labelledby="changeModalLabel<?= $category['catId'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="./save.php" method="POST">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="changeModalLabel<?= $category['catId'] ?>">
                                            Mise à jour de la catégorie
                                        </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $category['catId'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Nom :</label>
                                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name'] ?? ''); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description :</label>
                                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($category['description'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" name="validate" value="mise à jour" class="btn btn-primary">Modifier</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>

</body>

</html>