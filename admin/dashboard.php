<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../products/controller.php");
require_once(__DIR__ . "/../categories/controller.php");
require_once(__DIR__ . "/../auth/controller.php");
require_once(__DIR__ . "/../config/db.php");

// Protection : seul un utilisateur connecté peut accéder à cette page
if (!isset($_SESSION['user'])) {
    header("Location: /ecommerce/auth/login.php");
    exit();
}

$nombreProduits = getCountProduits($pdo);
$nombreCategories = getCountCategories($pdo);
$nombreClients = getCountClients($pdo);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">

    <!-- Inclusion de la barre latérale (Sidebar Admin) -->
    <?php include(__DIR__ . "/sidebar_admin.php"); ?>

    <!-- Zone principale décalee de 250px vers la droite -->
    <main style="margin-left: 250px;" class="p-4">
        <div class="container-fluid">

            <!-- En-tête du Dashboard -->
            <div class="mb-4">
                <h2 class="fw-bold text-dark mb-1">Tableau de bord</h2>
                <p class="text-muted">Bienvenue, <?= htmlspecialchars($_SESSION['user']['lastName'] ?? $_SESSION['user']['prenom'] ?? '') ?>.</p>
            </div>

            <!-- Cartes statistiques -->
            <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">

                <!-- Carte Produits -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 dashboard-card p-3">
                        <div class="card-body">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-box2" viewBox="0 0 16 16">
                                <path d="M2.95.4a1 1 0 0 1 .8-.4h8.5a1 1 0 0 1 .8.4l2.85 3.8a.5.5 0 0 1 .1.3V15a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4.5a.5.5 0 0 1 .1-.3zM7.5 1H3.75L1.5 4h6zm1 0v3h6l-2.25-3zM15 5H1v10h14z" />
                            </svg>
                            <h3 class="fw-bold mb-0"><?= $nombreProduits; ?></h3>
                            <p class="text-muted mb-0">Produits</p>
                        </div>
                    </div>
                </div>

                <!-- Carte Catégories -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 dashboard-card p-3">
                        <div class="card-body">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-archive text-warning mb-2" viewBox="0 0 16 16">
                                <path d="M0 2a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 12.5V5a1 1 0 0 1-1-1zm2 3v7.5A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5V5zm13-3H1v2h14zM5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
                            </svg>
                            <h3 class="fw-bold mb-0"><?= $nombreCategories; ?></h3>
                            <p class="text-muted mb-0">Catégories</p>
                        </div>
                    </div>
                </div>

                <!-- Carte Clients -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 dashboard-card p-3">
                        <div class="card-body">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-people text-success mb-2" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                            </svg>
                            <h3 class="fw-bold mb-0"><?= $nombreClients; ?></h3>
                            <p class="text-muted mb-0">Clients inscrits</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Graphique des produits les plus vendus -->
            <div class="card border-0 shadow-sm p-4">
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
    </main>

    <script src="../assets/js/bootstrap.js"></script>
</body>

</html>