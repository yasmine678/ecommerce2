<?php
require_once(__DIR__ . "/../config/db.php");

function getCartByUser(int $userId, PDO $pdo)
{
    $req = "SELECT cart.cId, cart.cquantity, product.proId, product.proName, 
                   product.price, product.image
            FROM cart
            JOIN product ON cart.prodId = product.proId
            WHERE cart.usId = :usId";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':usId' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLigneCart(int $userId, int $prodId, PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE usId = :usId AND prodId = :prodId");
    $stmt->execute([':usId' => $userId, ':prodId' => $prodId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insererLigneCart(int $userId, int $prodId, int $quantity, PDO $pdo)
{
    $stmt = $pdo->prepare("INSERT INTO cart (usId, prodId, cquantity) VALUES (:usId, :prodId, :cquantity)");
    return $stmt->execute([':usId' => $userId, ':prodId' => $prodId, ':cquantity' => $quantity]);
}

function mettreAJourQuantiteCart(int $cartId, int $quantity, PDO $pdo)
{
    $stmt = $pdo->prepare("UPDATE cart SET cquantity = :cquantity WHERE cId = :cId");
    return $stmt->execute([':cquantity' => $quantity, ':cid' => $cartId]);
}

function supprimerLigneCart(int $cartId, PDO $pdo)
{
    $stmt = $pdo->prepare("DELETE FROM cart WHERE cId = :cId");
    return $stmt->execute([':cId' => $cartId]);
}

function viderCart(int $userId, PDO $pdo)
{
    $stmt = $pdo->prepare("DELETE FROM cart WHERE usId = :usId");
    return $stmt->execute([':usId' => $userId]);
}