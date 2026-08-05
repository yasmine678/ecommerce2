<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../config/db.php");

$prodId = !empty($_POST['proId']) ? (int) $_POST['proId'] : null;
$servId = !empty($_POST['servId']) ? (int) $_POST['servId'] : null;
$quantite = (int) ($_POST['cquantity'] ?? 1);

if (!isset($_SESSION['user'])) {
    if (($prodId || $servId) && $quantite >= 1) {
        $_SESSION['pending_cart'] = [
            'proId' => $prodId,
            'servId' => $servId,
            'quantity' => $quantite,
        ];
    }
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user']['usId'];

if ((!$prodId && !$servId) || $quantite < 1) {
    header("Location: ../products/index.php?erreur=donnees_invalides");
    exit();
}

$ligneExistante = getLigneCart($userId, $prodId, $servId, $pdo);

if ($ligneExistante) {
    mettreAJourQuantiteCart($ligneExistante['cId'], $ligneExistante['cquantity'] + $quantite, $pdo);
} else {
    insererLigneCart($userId, $prodId, $servId, $quantite, $pdo);
}

header("Location: index.php?succes=1");
exit();