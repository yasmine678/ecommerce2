<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../cart/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: /ecommerce/auth/login.php");
    exit();
}

$userId = $_SESSION['user']['usId'];
$lignesCart = getCartByUser($userId, $pdo);

if (empty($lignesCart)) {
    header("Location: /ecommerce/cart/index.php?erreur=panier_vide");
    exit();
}

foreach ($lignesCart as $ligne) {
    creerCommande($userId, $ligne['proId'], $ligne['cquantity'], $pdo);
}

viderCart($userId, $pdo);

header("Location: /ecommerce/order/index.php?succes=1");
exit();