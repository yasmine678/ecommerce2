<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit();
}

$motCle = trim($_GET['q'] ?? '');

function rechercherCategories(string $mot, PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM category WHERE name LIKE :mot OR description LIKE :mot ORDER BY name ASC");
    $stmt->execute([':mot' => '%' . $mot . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function rechercherProduitsAdmin(string $mot, PDO $pdo)
{
    $req = "SELECT product.*, category.name AS catName
            FROM product
            LEFT JOIN category ON product.catId = category.catId
            WHERE product.proName LIKE :mot OR product.prodescription LIKE :mot
            ORDER BY product.proName ASC";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':mot' => '%' . $mot . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function rechercherServicesAdmin(string $mot, PDO $pdo)
{
    $req = "SELECT service.*, category.name AS catName
            FROM service
            LEFT JOIN category ON service.catId = category.catId
            WHERE service.servName LIKE :mot OR service.description LIKE :mot
            ORDER BY service.servName ASC";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':mot' => '%' . $mot . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function rechercherUtilisateurs(string $mot, PDO $pdo)
{
    $req = "SELECT * FROM users
            WHERE firstName LIKE :mot OR lastName LIKE :mot OR email LIKE :mot
            ORDER BY lastName ASC";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':mot' => '%' . $mot . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$categories = [];
$produits = [];
$services = [];
$utilisateurs = [];

if ($motCle !== '') {
    $categories = rechercherCategories($motCle, $pdo);
    $produits = rechercherProduitsAdmin($motCle, $pdo);
    $services = rechercherServicesAdmin($motCle, $pdo);
    $utilisateurs = rechercherUtilisateurs($motCle, $pdo);
}

$total = count($categories) + count($produits) + count($services) + count($utilisateurs);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche admin - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>
<body>
    <?php include(__DIR__ . "/../admin/sidebar_admin.php"); ?>

    <main class="admin-main container-fluid px-4 my-4">
        <h2 class="page-title mb-4">Recherche</h2>

        <?php if ($motCle === ''): ?>
            <p class="text-muted">Saisissez un mot-clé pour lancer une recherche.</p>
        <?php else: ?>
            <p class="text-muted mb-4"><?= $total ?> résultat<?= $total > 1 ? 's' : '' ?> pour « <?= htmlspecialchars($motCle) ?> »</p>

            <?php if ($total === 0): ?>
                <div class="alert alert-info">Aucun résultat trouvé.</div>
            <?php endif; ?>

            <?php if (!empty($categories)): ?>
                <h5 class="mb-3">Catégories (<?= count($categories) ?>)</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-striped align-middle">
                        <thead><tr><th>Nom</th><th>Description</th><th class="text-end">Action</th></tr></thead>
                        <tbody>
                            <?php foreach ($categories as $categorie): ?>
                                <tr>
                                    <td><?= htmlspecialchars($categorie['name']) ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars(mb_strimwidth($categorie['description'] ?? '', 0, 80, '…')) ?></td>
                                    <td class="text-end">
                                        <a href="../categories/index.php" class="btn btn-sm btn-outline-primary">Gérer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if (!empty($produits)): ?>
                <h5 class="mb-3">Produits (<?= count($produits) ?>)</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-striped align-middle">
                        <thead><tr><th>Nom</th><th>Catégorie</th><th>Prix</th><th class="text-end">Action</th></tr></thead>
                        <tbody>
                            <?php foreach ($produits as $produit): ?>
                                <tr>
                                    <td><?= htmlspecialchars($produit['proName']) ?></td>
                                    <td><?= htmlspecialchars($produit['catName'] ?? 'Sans catégorie') ?></td>
                                    <td><?= number_format($produit['price'], 0, ',', ' ') ?> FCFA</td>
                                    <td class="text-end">
                                        <a href="../products/index.php" class="btn btn-sm btn-outline-primary">Gérer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if (!empty($services)): ?>
                <h5 class="mb-3">Services (<?= count($services) ?>)</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-striped align-middle">
                        <thead><tr><th>Nom</th><th>Catégorie</th><th>Tarif</th><th class="text-end">Action</th></tr></thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td><?= htmlspecialchars($service['servName']) ?></td>
                                    <td><?= htmlspecialchars($service['catName'] ?? 'Sans catégorie') ?></td>
                                    <td><?= number_format($service['priceHours'], 0, ',', ' ') ?> FCFA/<?= htmlspecialchars($service['unite'] ?? 'heure') ?></td>
                                    <td class="text-end">
                                        <a href="../services/index.php" class="btn btn-sm btn-outline-primary">Gérer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if (!empty($utilisateurs)): ?>
                <h5 class="mb-3">Utilisateurs (<?= count($utilisateurs) ?>)</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-striped align-middle">
                        <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th class="text-end">Action</th></tr></thead>
                        <tbody>
                            <?php foreach ($utilisateurs as $utilisateur): ?>
                                <tr>
                                    <td><?= htmlspecialchars(($utilisateur['firstName'] ?? '') . ' ' . ($utilisateur['lastName'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $utilisateur['role'] === 'manager' ? 'primary' : 'secondary' ?>">
                                            <?= htmlspecialchars($utilisateur['role']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="../admin/users.php" class="btn btn-sm btn-outline-primary">Gérer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <script src="../assets/js/bootstrap.js"></script>
</body>
</html>
