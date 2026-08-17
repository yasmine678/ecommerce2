<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../cart/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user']['usId'];
$lignesCart = getCartByUser($userId, $pdo);

if (empty($lignesCart)) {
    header("Location: ../cart/index.php?erreur=panier_vide");
    exit();
}

$note = trim($_POST['note'] ?? '');

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
    <title>Paiement - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>
<body class="client">
    <?php include(__DIR__ . "/../includes/header.php"); ?>

    <div class="container my-5 below-header" style="max-width: 720px;">
        <h2 class="mb-4">Paiement</h2>

        <div class="alert alert-info small">
            Paiement de démonstration : aucune transaction réelle n'est effectuée. Cette étape simule un paiement pour valider votre commande.
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Récapitulatif</h5>
                <ul class="list-group list-group-flush mb-3">
                    <?php foreach ($lignesCart as $ligne): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><?= htmlspecialchars($ligne['proName']) ?> <span class="text-muted">x<?= $ligne['cquantity'] ?></span></span>
                            <span class="fw-semibold"><?= number_format($ligne['price'] * $ligne['cquantity'], 0, ',', ' ') ?> FCFA</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Total à payer</span>
                    <span class="fw-bold text-warning fs-4"><?= number_format($montantTotal, 0, ',', ' ') ?> FCFA</span>
                </div>
            </div>
        </div>

        <form action="./valider.php" method="POST" class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Mode de paiement</h5>
                <input type="hidden" name="note" value="<?= htmlspecialchars($note) ?>">

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="methodeCarte" value="carte" checked>
                        <label class="form-check-label" for="methodeCarte">Carte bancaire</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="methodeFlooz" value="flooz">
                        <label class="form-check-label" for="methodeFlooz">Mobile Money (Flooz)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="methodeTmoney" value="tmoney">
                        <label class="form-check-label" for="methodeTmoney">Mobile Money (T-Money)</label>
                    </div>
                </div>

                <div id="blocCarte">
                    <div class="row g-2 mb-2">
                        <div class="col-12">
                            <label class="form-label small">Numéro de carte</label>
                            <input type="text" class="form-control" placeholder="4242 4242 4242 4242" maxlength="19" inputmode="numeric">
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small">Expiration</label>
                            <input type="text" class="form-control" placeholder="MM/AA" maxlength="5">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">CVV</label>
                            <input type="text" class="form-control" placeholder="123" maxlength="3" inputmode="numeric">
                        </div>
                    </div>
                </div>

                <div id="blocMobileMoney" class="mb-2" style="display: none;">
                    <label class="form-label small">Numéro de téléphone</label>
                    <input type="text" class="form-control" placeholder="90 00 00 00">
                </div>

                <p class="text-muted small mb-0">Ces champs sont fictifs (démonstration) et ne sont ni envoyés ni conservés.</p>
            </div>
            <div class="card-footer bg-white border-0 pb-4">
                <button type="submit" class="btn btn-warning btn-lg w-100">
                    Payer <?= number_format($montantTotal, 0, ',', ' ') ?> FCFA
                </button>
            </div>
        </form>
    </div>

    <?php include(__DIR__ . "/../includes/footer.php"); ?>
    <script src="../assets/js/bootstrap.js"></script>
    <script>
        (function () {
            var radios = document.querySelectorAll('input[name="paymentMethod"]');
            var blocCarte = document.getElementById("blocCarte");
            var blocMobileMoney = document.getElementById("blocMobileMoney");

            function majAffichage() {
                var choix = document.querySelector('input[name="paymentMethod"]:checked').value;
                blocCarte.style.display = choix === "carte" ? "" : "none";
                blocMobileMoney.style.display = choix === "carte" ? "none" : "";
            }

            radios.forEach(function (r) { r.addEventListener("change", majAffichage); });
            majAffichage();
        })();
    </script>
</body>
</html>
