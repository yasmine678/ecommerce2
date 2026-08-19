<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user']['usId'];
$lignesCart = getCartByUser($userId, $pdo);

$montantTotal = 0;
foreach ($lignesCart as $ligne) {
    $montantTotal += $ligne['price'] * $ligne['cquantity'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>
<body class="client">
    <?php include(__DIR__ . "/../includes/header.php"); ?>

    <div class="container my-5 below-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <h2 class="m-0">Mon panier</h2>
            <a href="../order/index.php" class="btn btn-outline-primary d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                    <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z" />
                    <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z" />
                    <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5" />
                </svg>
                Voir mes commandes
            </a>
        </div>

        <?php if (isset($_GET['succes'])): ?>
            <div class="alert alert-success">Panier mis à jour.</div>
        <?php endif; ?>

        <?php if (empty($lignesCart)): ?>
            <p class="text-muted">Votre panier est vide.</p>
            <a href="../products/index.php" class="btn btn-warning">Voir les produits</a>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Sous-total</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lignesCart as $ligne): ?>
                            <tr>
                                <td class="d-flex align-items-center gap-2">
                                    <?php if (!empty($ligne['simage'])): ?>
                                        <img src="../uploads/<?= $ligne['itemType'] === 'service' ? 'services' : 'products' ?>/<?= htmlspecialchars($ligne['simage']) ?>"
                                             style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                    <?php endif; ?>
                                    <?= htmlspecialchars($ligne['proName']) ?>
                                </td>
                                <td><?= number_format($ligne['price'], 0, ',', ' ') ?> FCFA</td>
                                <td style="width: 100px;">
                                    <input type="number" class="form-control form-control-sm quantite-panier"
                                           data-cid="<?= $ligne['cId'] ?>" data-price="<?= $ligne['price'] ?>"
                                           value="<?= $ligne['cquantity'] ?>" min="1">
                                </td>
                                <td class="fw-bold sous-total">
                                    <?= number_format($ligne['price'] * $ligne['cquantity'], 0, ',', ' ') ?> FCFA
                                </td>
                                <td class="text-end">
                                    <form action="./save.php" method="POST"
                                          onsubmit="return confirm('Retirer ce produit du panier ?');">
                                        <input type="hidden" name="cId" value="<?= $ligne['cId'] ?>">
                                        <button type="submit" name="validate" value="Supprimer"
                                                class="btn btn-sm btn-outline-danger"
                                                aria-label="Retirer" title="Retirer">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L13.882 4zM2.5 3h11V2h-11z" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total :</td>
                            <td colspan="2" class="fw-bold text-warning fs-5" id="totalPanier">
                                <?= number_format($montantTotal, 0, ',', ' ') ?> FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <form action="../paiement/index.php" method="POST">
                <div class="mb-3">
                    <label for="note" class="form-label">
                        Une précision pour le vendeur ? (optionnel)
                    </label>
                    <textarea name="note" id="note" class="form-control" rows="3"
                        placeholder="Ex : couleur souhaitée, adresse de livraison précise, créneau horaire..."></textarea>
                    <div class="form-text">Ce message ne sera visible que par le manager.</div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-warning btn-lg">
                        Passer la commande
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <?php include(__DIR__ . "/../includes/footer.php"); ?>
    <script src="../assets/js/bootstrap.js"></script>
    <script>
        (function () {
            var champsQuantite = document.querySelectorAll(".quantite-panier");
            var totalPanier = document.getElementById("totalPanier");
            if (!champsQuantite.length || !totalPanier) return;

            function formaterPrix(prix) {
                return Number(prix || 0).toLocaleString("fr-FR") + " FCFA";
            }

            function recalculerTout() {
                var total = 0;
                champsQuantite.forEach(function (champ) {
                    var qte = Math.max(1, parseInt(champ.value, 10) || 1);
                    var prix = parseFloat(champ.dataset.price) || 0;
                    var sousTotal = qte * prix;
                    var cellule = champ.closest("tr").querySelector(".sous-total");
                    if (cellule) cellule.textContent = formaterPrix(sousTotal);
                    total += sousTotal;
                });
                totalPanier.textContent = formaterPrix(total);
            }

            champsQuantite.forEach(function (champ) {
                var minuteur = null;
                champ.addEventListener("input", function () {
                    recalculerTout();

                    clearTimeout(minuteur);
                    minuteur = setTimeout(function () {
                        var qte = Math.max(1, parseInt(champ.value, 10) || 1);
                        fetch("./save.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: new URLSearchParams({
                                cId: champ.dataset.cid,
                                cquantity: qte,
                                validate: "Modifier"
                            })
                        }).catch(function () { /* silencieux : on retentera au prochain changement */ });
                    }, 600);
                });
            });
        })();
    </script>
</body>
</html>