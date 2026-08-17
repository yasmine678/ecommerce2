<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../order/controller.php");
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

$methodesValides = ['carte', 'flooz', 'tmoney'];
$paymentMethod = $_POST['paymentMethod'] ?? '';

if (!in_array($paymentMethod, $methodesValides, true)) {
    header("Location: ../paiement/index.php?erreur=methode_invalide");
    exit();
}

$note = trim($_POST['note'] ?? '');
$note = $note !== '' ? $note : null;

creerCommande($userId, $lignesCart, $pdo, $note, $paymentMethod, 'payee');

viderCart($userId, $pdo);

header("Location: ../order/index.php?succes=1&paye=1");
exit();
