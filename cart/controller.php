<?php
require_once(__DIR__ . "/../config/db.php");

function getCartByUser(int $userId, PDO $pdo)
{
    $req = "SELECT cart.cId, cart.cquantity, cart.prodId, cart.servId,
                   COALESCE(product.proName, service.servName) AS proName,
                   COALESCE(product.price, service.priceHours) AS price,
                   COALESCE(product.image, service.image) AS image,
                   CASE WHEN cart.servId IS NOT NULL THEN 'service' ELSE 'product' END AS itemType
            FROM cart
            LEFT JOIN product ON cart.prodId = product.proId
            LEFT JOIN service ON cart.servId = service.servId
            WHERE cart.usId = :usId";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':usId' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLigneCart(int $userId, ?int $prodId, ?int $servId, PDO $pdo)
{
    if ($servId !== null) {
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE usId = :usId AND servId = :servId");
        $stmt->execute([':usId' => $userId, ':servId' => $servId]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE usId = :usId AND prodId = :prodId");
        $stmt->execute([':usId' => $userId, ':prodId' => $prodId]);
    }
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insererLigneCart(int $userId, ?int $prodId, ?int $servId, int $quantity, PDO $pdo)
{
    $stmt = $pdo->prepare("INSERT INTO cart (usId, prodId, servId, cquantity) VALUES (:usId, :prodId, :servId, :cquantity)");
    return $stmt->execute([':usId' => $userId, ':prodId' => $prodId, ':servId' => $servId, ':cquantity' => $quantity]);
}

function mettreAJourQuantiteCart(int $cartId, int $quantity, PDO $pdo)
{
    $stmt = $pdo->prepare("UPDATE cart SET cquantity = :cquantity WHERE cId = :cId");
    return $stmt->execute([':cquantity' => $quantity, ':cId' => $cartId]);
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