<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: /ecommerce/auth/login.php");
    exit();
}

$userId = $_SESSION['user']['usId'];
$prodId = $_POST['proId'] ?? null;
$quantite = (int) ($_POST['cquantity'] ?? 1);

if (empty($prodId) || $quantite < 1) {
    header("Location: /ecommerce/products/index.php?erreur=donnees_invalides");
    exit();
}

$ligneExistante = getLigneCart($userId, $prodId, $pdo);

if ($ligneExistante) {
    mettreAJourQuantiteCart($ligneExistante['cId'], $ligneExistante['cquantity'] + $quantite, $pdo);
} else {
    insererLigneCart($userId, $prodId, $quantite, $pdo);
}

header("Location: /ecommerce/cart/index.php?succes=1");
exit();