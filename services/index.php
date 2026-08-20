<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../categories/controller.php");
require_once(__DIR__ . "/../cart/controller.php");

// 1. Vérification du rôle Administrateur
$isAdmin = isset($_SESSION['user']['role']) && (strtolower(trim($_SESSION['user']['role'])) === 'manager');
$quantitesPanier = (!$isAdmin && isset($_SESSION['user'])) ? getCartQuantitiesByUser($_SESSION['user']['usId'], $pdo) : [];

// 2. Gestion des données et du filtre par catégorie
$categories = getAll($pdo);
$categoryId = $_GET['category_id'] ?? $_GET['catId'] ?? null;
$tousLesServices = getAllServices($pdo);

if ($categoryId) {
    $tousLesServices = array_values(array_filter($tousLesServices, function ($s) use ($categoryId) {
        return (int) $s['catId'] === (int) $categoryId;
    }));
}

// Côté client, on ne montre que les services disponibles
$services = $isAdmin ? $tousLesServices : array_values(array_filter($tousLesServices, function ($s) {
    return (bool) $s['available'];
}));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isAdmin ? "Gestion des Services" : "Nos Services" ?> - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
    <script src="../assets/js/bootstrap.js" defer></script>
</head>

<body class="<?= $isAdmin ? 'bg-light' : 'client' ?>">

    <?php if ($isAdmin): ?>
        <!-- Barre latérale d'administration -->
        <?php include(__DIR__ . "/../admin/sidebar_admin.php"); ?>
    <?php else: ?>
        <!-- En-tête public / client -->
        <?php include(__DIR__ . "/../includes/header.php"); ?>
    <?php endif; ?>

    <datalist id="listeUnites">
        <option value="heure">
        <option value="minute">
        <option value="jour">
        <option value="taille">
        <option value="projet">
        <option value="forfait">
    </datalist>

    <main class="p-4<?= $isAdmin ? ' admin-main' : ' below-header' ?>">
        <div class="container<?= $isAdmin ? '-fluid' : '' ?>">

            <div class="<?= $isAdmin ? '' : 'page-banner' ?> d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title text-dark mb-1">
                        <?= $isAdmin ? "Gestion des Services" : "Nos Services" ?>
                    </h2>
                    <p class="text-muted mb-0">
                        <?= $isAdmin ? "Consultez, ajoutez ou modifiez les services proposés." : "Découvrez nos prestations disponibles." ?>
                    </p>
                </div>

                <?php if ($isAdmin): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        + Ajouter
                    </button>

                    <!-- Ajout de service -->
                    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="./save.php" method="POST" enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="addModalLabel">Ajouter</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nom du service</label>
                                            <input type="text" name="servName" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="3"></textarea>
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-sm-6">
                                                <label class="form-label">Tarif (FCFA)</label>
                                                <input type="number" name="price" step="0.01" class="form-control" required>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label">Unité</label>
                                                <input type="text" name="unite" class="form-control" list="listeUnites" placeholder="heure" value="heure">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Prestataire</label>
                                            <input type="text" name="provider" class="form-control" placeholder="Nom de l'employé assigné">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Catégorie</label>
                                            <select name="catId" class="form-select" required>
                                                <option value="">-- Sélectionner une catégorie --</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category['catId'] ?>">
                                                        <?= htmlspecialchars($category['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" name="enPromotion" value="1" id="enPromotion_add" class="form-check-input">
                                            <label for="enPromotion_add" class="form-check-label">En promotion</label>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Prix promo (FCFA)</label>
                                            <input type="number" name="prixPromo" step="0.01" class="form-control" placeholder="Prix réduit, si en promotion">
                                        </div>
                                        <div class="mb-3">
                                            <label for="image_add_service" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 mb-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
                                                </svg>
                                                Ajouter une image
                                            </label>
                                            <input type="file" name="image" id="image_add_service" accept="image/*" class="visually-hidden">
                                            <div class="form-text small text-muted mt-1 fichier-nom"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Statut</label>
                                            <select name="available" class="form-select">
                                                <option value="1" selected>Disponible</option>
                                                <option value="0">Indisponible</option>
                                            </select>
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

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($services)): ?>
                <div class="alert alert-info text-center py-4">
                    <span>Aucun service <?= $isAdmin ? 'trouvé' : 'disponible pour le moment' ?>.</span>
                </div>
            <?php else: ?>

                <?php if ($isAdmin): ?>
                    <!-- VUE ADMINISTRATEUR -->
                    <div class="row g-3 cartes-animees">
                        <?php foreach ($services as $service): ?>
                            <div class="col-12">
                                <div class="card admin-row-card border-0 shadow-sm p-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                                        <span class="d-inline-flex align-items-center justify-content-center">
                                            <?php if (!empty($service['serImage'])): ?>
                                                <img src="../uploads/services/<?= htmlspecialchars($service['serImage']) ?>" alt="Image service" class="rounded border" width="50" height="50" style="object-fit: cover;">
                                            <?php else: ?>
                                                <span class="badge bg-secondary text-white p-3 rounded border">No Img</span>
                                            <?php endif; ?>
                                        </span>

                                        <span class="badge bg-dark text-white px-3 py-2 fs-6">
                                            <?= htmlspecialchars($service['servName'] ?? '') ?>
                                        </span>

                                        <span class="badge bg-light text-secondary border px-3 py-2 text-truncate" style="max-width: 220px;">
                                            <?= htmlspecialchars($service['description'] ?? 'Pas de description') ?>
                                        </span>

                                        <span class="badge bg-info text-dark bg-opacity-25 px-3 py-2 border border-info border-opacity-25 rounded-pill">
                                            <?= htmlspecialchars($service['name'] ?? 'Sans catégorie') ?>
                                        </span>

                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2">
                                            <?= !empty($service['provider']) ? htmlspecialchars($service['provider']) : 'Prestataire non précisé' ?>
                                        </span>

                                        <?php $estDisponible = (bool) $service['available']; ?>
                                        <span class="badge bg-<?= $estDisponible ? 'success' : 'danger' ?> bg-opacity-10 text-<?= $estDisponible ? 'success' : 'danger' ?> border border-<?= $estDisponible ? 'success' : 'danger' ?> border-opacity-25 px-3 py-2">
                                            <?= $estDisponible ? 'Disponible' : 'Indisponible' ?>
                                        </span>

                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 fs-6">
                                            <?= number_format($service['priceHours'], 0, ',', ' ') ?> FCFA/<?= htmlspecialchars($service['unite'] ?? 'heure') ?>
                                        </span>

                                        <span class="d-inline-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changeModal<?= $service['servId'] ?>"
                                                    aria-label="Modifier" title="Modifier">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.114.168l-.803 2.008a.25.25 0 0 0 .32.32l2.008-.803a.5.5 0 0 0 .168-.115l6.813-6.812z" />
                                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                </svg>
                                            </button>

                                            <form action="./save.php" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce service ?');">
                                                <input type="hidden" name="servId" value="<?= $service['servId'] ?>">
                                                <button type="submit" name="validate" value="Supprimer" class="btn btn-sm btn-outline-danger"
                                                        aria-label="Supprimer" title="Supprimer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L13.882 4zM2.5 3h11V2h-11z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </span>

                                    </div>
                                </div>
                            </div>

                            <!-- Modification -->
                            <div class="modal fade" id="changeModal<?= $service['servId'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="./save.php" method="POST" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">Mise à jour du service</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="servId" value="<?= $service['servId']; ?>">

                                                <div class="mb-3">
                                                    <label class="form-label">Nom du service :</label>
                                                    <input type="text" name="servName" class="form-control" value="<?= htmlspecialchars($service['servName'] ?? ''); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description :</label>
                                                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($service['description'] ?? ''); ?></textarea>
                                                </div>
                                                <div class="row g-3 mb-3">
                                                    <div class="col-sm-6">
                                                        <label class="form-label">Tarif (FCFA) :</label>
                                                        <input type="number" step="0.01" name="priceHours" class="form-control" value="<?= htmlspecialchars($service['priceHours'] ?? 0); ?>" required>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="form-label">Unité :</label>
                                                        <input type="text" name="unite" class="form-control" list="listeUnites" value="<?= htmlspecialchars($service['unite'] ?? 'heure'); ?>">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Prestataire :</label>
                                                    <input type="text" name="provider" class="form-control" value="<?= htmlspecialchars($service['provider'] ?? ''); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catégorie :</label>
                                                    <select name="catId" class="form-select" required>
                                                        <?php foreach ($categories as $category): ?>
                                                            <option value="<?= $category['catId']; ?>" <?= ($category['catId'] == $service['catId']) ? 'selected' : ''; ?>>
                                                                <?= htmlspecialchars($category['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3 form-check">
                                                    <input type="checkbox" name="enPromotion" value="1" id="enPromotion_<?= $service['servId'] ?>" class="form-check-input" <?= !empty($service['enPromotion']) ? 'checked' : '' ?>>
                                                    <label for="enPromotion_<?= $service['servId'] ?>" class="form-check-label">En promotion</label>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Prix promo (FCFA) :</label>
                                                    <input type="number" step="0.01" name="prixPromo" class="form-control" value="<?= htmlspecialchars($service['prixPromo'] ?? '') ?>" placeholder="Prix réduit, si en promotion">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label d-block mb-1">
                                                        Image actuelle :
                                                        <span class="text-muted"><?= !empty($service['serImage']) ? htmlspecialchars($service['serImage']) : 'aucune' ?></span>
                                                    </label>
                                                    <label for="image_serv_<?= $service['servId'] ?>" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 mt-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                                                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                                            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
                                                        </svg>
                                                        Changer l'image
                                                    </label>
                                                    <input type="file" name="image" id="image_serv_<?= $service['servId'] ?>" accept="image/*" class="visually-hidden">
                                                    <div class="form-text small text-muted mt-1 fichier-nom"></div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Statut :</label>
                                                    <select name="available" class="form-select">
                                                        <option value="1" <?= $service['available'] ? 'selected' : '' ?>>Disponible</option>
                                                        <option value="0" <?= !$service['available'] ? 'selected' : '' ?>>Indisponible</option>
                                                    </select>
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
                    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4 cartes-animees">
                        <?php foreach ($services as $service): ?>
                            <div class="col">
                                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden d-flex flex-column service-card">

                                    <?php $qte = $quantitesPanier['service_' . $service['servId']] ?? 0; ?>
                                    <span class="panier-badge-carte <?= $qte > 0 ? '' : 'd-none' ?>" data-panier-key="service_<?= $service['servId'] ?>">×<?= $qte ?></span>

                                    <a href="./detail.php?id=<?= $service['servId'] ?>" class="text-decoration-none text-dark">
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px; width: 100%;">
                                            <?php if (!empty($service['serImage'])): ?>
                                                <img src="../uploads/services/<?= htmlspecialchars($service['serImage']) ?>"
                                                    class="card-img-top w-100 h-100"
                                                    alt="<?= htmlspecialchars($service['servName'] ?? 'Service') ?>"
                                                    style="object-fit: cover;">
                                            <?php else: ?>
                                                <div class="text-center text-muted">
                                                    <span class="fs-1">S</span>
                                                    <p class="small mb-0">Pas d'image</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="card-body pb-0">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="fw-bold text-dark mb-0">
                                                    <?= htmlspecialchars($service['servName'] ?? '') ?>
                                                </h5>
                                                <span class="badge bg-info text-dark bg-opacity-25 border border-info border-opacity-25 rounded-pill">
                                                    <?= htmlspecialchars($service['name'] ?? 'Général') ?>
                                                </span>
                                            </div>
                                            <p class="text-muted small mb-1 produit-description">
                                                <?= htmlspecialchars($service['description'] ?? 'Aucune description disponible.') ?>
                                            </p>
                                            <?php if (!empty($service['provider'])): ?>
                                                <p class="text-muted small mb-0">
                                                    Par <?= htmlspecialchars($service['provider']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </a>

                                    <div class="card-body pt-2 mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-warning fs-5">
                                                <?= number_format($service['priceHours'] ?? 0, 0, ',', ' ') ?> FCFA/<?= htmlspecialchars($service['unite'] ?? 'heure') ?>
                                            </span>
                                            <form action="../cart/add.php" method="POST" class="m-0 ajout-panier">
                                                <input type="hidden" name="servId" value="<?php echo $service['servId']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-basket2" viewBox="0 0 16 16">
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
        <script src="../assets/js/script.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/script.js'); ?>" defer></script>
    <?php endif; ?>

</body>

</html>
