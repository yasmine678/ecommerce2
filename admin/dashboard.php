<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../products/controller.php");
require_once(__DIR__ . "/../categories/controller.php");
require_once(__DIR__ . "/../auth/controller.php");
require_once(__DIR__ . "/../order/controller.php");
require_once(__DIR__ . "/../config/db.php");

// Protection : seul un utilisateur connecté peut accéder à cette page
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$nombreProduits = getCountProduits($pdo);
$nombreCategories = getCountCategories($pdo);
$nombreClients = getCountClients($pdo);
$topProduits = getProduitsPlusVendus($pdo, 5);
$nombreCommandesActives = getCountCommandesActives($pdo);
$categoriesProduits = getCategoriesAvecNombreProduits($pdo);
$totalProduitsCategories = array_sum(array_column($categoriesProduits, 'nombre'));
$couleursCamembert = ['#066a95', '#022f4e', '#3498db', '#0a591c', '#049729', '#300f64', '#851306', '#16a085'];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>

<body class="bg-light">

    <!-- Inclusion de la barre latérale (Sidebar Admin) -->
    <?php include(__DIR__ . "/sidebar_admin.php"); ?>

    <!-- Zone principale décalee de 250px vers la droite -->
    <main style="margin-left: 250px;" class="p-4">
        <div class="container-fluid">

            <!-- En-tête du Dashboard -->
            <div class="mb-4">
                <h2 class="page-title text-dark mb-1">Tableau de bord</h2>
                <p class="text-muted">Bienvenue, <?= htmlspecialchars($_SESSION['user']['lastName'] ?? $_SESSION['user']['prenom'] ?? '') ?>.</p>
            </div>

            <?php if (isset($_GET['nouvelles_commandes'])): ?>
                <div class="alert alert-warning">Vous avez de nouvelles commandes.</div>
            <?php endif; ?>

            <!-- Cartes statistiques -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3 mb-4">

                <!-- Carte Produits -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 dashboard-card p-3">
                        <div class="card-body">
                            <span class="icon-chip icon-chip-sky">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M2.95.4a1 1 0 0 1 .8-.4h8.5a1 1 0 0 1 .8.4l2.85 3.8a.5.5 0 0 1 .1.3V15a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4.5a.5.5 0 0 1 .1-.3zM7.5 1H3.75L1.5 4h6zm1 0v3h6l-2.25-3zM15 5H1v10h14z" />
                                </svg>
                            </span>
                            <h3 class="fw-bold mb-0"><?= $nombreProduits; ?></h3>
                            <p class="text-muted mb-0">Produits</p>
                        </div>
                    </div>
                </div>

                <!-- Carte Catégories -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 dashboard-card p-3">
                        <div class="card-body">
                            <span class="icon-chip icon-chip-gold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M0 2a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 12.5V5a1 1 0 0 1-1-1zm2 3v7.5A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5V5zm13-3H1v2h14zM5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
                                </svg>
                            </span>
                            <h3 class="fw-bold mb-0"><?= $nombreCategories; ?></h3>
                            <p class="text-muted mb-0">Catégories</p>
                        </div>
                    </div>
                </div>

                <!-- Carte Clients -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 dashboard-card p-3">
                        <div class="card-body">
                            <span class="icon-chip icon-chip-navy">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                                </svg>
                            </span>
                            <h3 class="fw-bold mb-0"><?= $nombreClients; ?></h3>
                            <p class="text-muted mb-0">Clients inscrits</p>
                        </div>
                    </div>
                </div>

                <!-- Carte Commandes -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 dashboard-card p-3">
                        <div class="card-body">
                            <span class="icon-chip icon-chip-gold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 10H4a.5.5 0 0 1-.485-.379L1.61 2H.5a.5.5 0 0 1-.5-.5M3.14 4l.5 2H4a.5.5 0 0 1 0 1H3.89l.5 2H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.485-.379L2.14 4z" />
                                    <path d="M6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0M13 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                                </svg>
                            </span>
                            <h3 class="fw-bold mb-0"><?= $nombreCommandesActives; ?></h3>
                            <p class="text-muted mb-0">Commandes</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row g-3">
            <div class="col-lg-6">
            <!-- Graphique des produits les plus vendus -->
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4">Produits les plus vendus</h5>

                <?php if (empty($topProduits)): ?>
                    <p class="text-muted m-0">Aucune vente enregistrée pour le moment.</p>
                <?php else: ?>
                    <?php
                    $largeur = 600;
                    $hauteur = 220;
                    $marge = 40;
                    $maxVente = max(array_column($topProduits, 'total_vendu')) ?: 1;
                    $nbPoints = count($topProduits);

                    // Calcul des coordonnées X, Y pour chaque point
                    $points = [];
                    foreach ($topProduits as $index => $produit) {
                        $x = ($nbPoints === 1)
                            ? $largeur / 2
                            : $marge + ($index * (($largeur - 2 * $marge) / ($nbPoints - 1)));
                        $y = $hauteur - $marge - (($produit['total_vendu'] / $maxVente) * ($hauteur - 2 * $marge));
                        $points[] = [
                            'x' => round($x, 1),
                            'y' => round($y, 1),
                            'val' => $produit['total_vendu'],
                            'label' => $produit['proName'] ?? ''
                        ];
                    }
                    ?>

                    <div class="position-relative w-100 bg-white rounded border overflow-hidden" style="height: <?= $hauteur ?>px;">

                        <!-- Lignes de repère horizontales -->
                        <?php for ($i = 0; $i <= 4; $i++): ?>
                            <?php $yLigne = $marge + ($i * (($hauteur - 2 * $marge) / 4)); ?>
                            <div class="position-absolute w-100 border-top border-secondary opacity-25"
                                style="top: <?= $yLigne ?>px;"></div>
                        <?php endfor; ?>

                        <!-- Lignes reliant les points -->
                        <?php for ($i = 0; $i < count($points) - 1; $i++): ?>
                            <?php
                            $p1 = $points[$i];
                            $p2 = $points[$i + 1];

                            $dx = $p2['x'] - $p1['x'];
                            $dy = $p2['y'] - $p1['y'];
                            $longueur = sqrt($dx * $dx + $dy * $dy);
                            $angle = rad2deg(atan2($dy, $dx));
                            ?>
                            <div class="position-absolute bg-warning"
                                style="height: 3px; 
                                        width: <?= $longueur ?>px; 
                                        top: <?= $p1['y'] ?>px; 
                                        left: <?= $p1['x'] ?>px; 
                                        transform-origin: 0 0; 
                                        transform: rotate(<?= $angle ?>deg); 
                                        z-index: 1;">
                            </div>
                        <?php endfor; ?>

                        <!-- Points et étiquettes -->
                        <?php foreach ($points as $pt): ?>
                            <!-- Point -->
                            <div class="position-absolute bg-warning rounded-circle border border-2 border-white shadow-sm"
                                style="width: 12px; 
                                        height: 12px; 
                                        top: <?= $pt['y'] - 6 ?>px; 
                                        left: <?= $pt['x'] - 6 ?>px; 
                                        z-index: 2;">
                            </div>

                            <!-- Valeur vendue -->
                            <div class="position-absolute text-center fw-bold text-dark"
                                style="top: <?= $pt['y'] - 24 ?>px; 
                                        left: <?= $pt['x'] - 20 ?>px; 
                                        width: 40px; 
                                        font-size: 12px; 
                                        z-index: 2;">
                                <?= $pt['val'] ?>
                            </div>

                            <!-- Nom du produit -->
                            <div class="position-absolute text-center text-muted"
                                style="top: <?= $hauteur - $marge + 10 ?>px; 
                                        left: <?= $pt['x'] - 40 ?>px; 
                                        width: 80px; 
                                        font-size: 11px; 
                                        z-index: 2;">
                                <?= htmlspecialchars(mb_strimwidth($pt['label'], 0, 10, '…')) ?>
                            </div>
                        <?php endforeach; ?>

                    </div>
                <?php endif; ?>
            </div>
            </div>

            <div class="col-lg-6">
          
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4">Catégories par nombre de produits</h5>

                <?php if (empty($categoriesProduits) || $totalProduitsCategories === 0): ?>
                    <p class="text-muted m-0">Aucune catégorie avec des produits pour le moment.</p>
                <?php else: ?>
                    <?php
                    $degrades = [];
                    $cumule = 0;
                    foreach ($categoriesProduits as $i => $cat) {
                        $part = ($cat['nombre'] / $totalProduitsCategories) * 100;
                        $debut = $cumule;
                        $cumule += $part;
                        $couleur = $couleursCamembert[$i % count($couleursCamembert)];
                        $degrades[] = "$couleur " . round($debut, 2) . "% " . round($cumule, 2) . "%";
                    }
                    ?>

                    <div class="d-flex flex-column flex-sm-row align-items-center gap-4">
                        <div class="position-relative flex-shrink-0" style="width: 180px; height: 180px;">
                            <div class="rounded-circle w-100 h-100"
                                style="background: conic-gradient(<?= implode(', ', $degrades) ?>);">
                            </div>
                            <div class="rounded-circle position-absolute bg-white"
                                style="width: 100px; height: 100px; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            </div>
                        </div>

                        <ul class="list-unstyled m-0 w-100">
                            <?php foreach ($categoriesProduits as $i => $cat): ?>
                                <li class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle d-inline-block"
                                            style="width: 12px; height: 12px; background-color: <?= $couleursCamembert[$i % count($couleursCamembert)] ?>;"></span>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </span>
                                    <span class="text-muted"><?= $cat['nombre'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            </div>
            </div>

        </div>
    </main>

    <script src="../assets/js/bootstrap.js"></script>
</body>

</html>