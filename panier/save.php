<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: /ecommerce/auth/login.php");
    exit();
}

if (isset($_POST['validate']) && $_POST['validate'] !== '') {

    $validate = $_POST['validate'];

    if ($validate == "Modifier") {

        $cartId = $_POST['cId'];
        $quantite = (int) $_POST['cquantity'];

        if ($quantite < 1) {
            header("Location: /ecommerce/cart/index.php?erreur=quantite_invalide");
            exit();
        }

        mettreAJourQuantiteCart($cartId, $quantite, $pdo);
        header("Location: /ecommerce/cart/index.php?succes=1");
        exit();

    } else if ($validate == "Supprimer") {

        $cartId = $_POST['cartId'];
        supprimerLigneCart($cartId, $pdo);
        header("Location: /ecommerce/cart/index.php?succes=1");
        exit();

    } else {
        echo "Formulaire inconnu";
    }
}