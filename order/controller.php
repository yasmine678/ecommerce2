<?php
require_once(__DIR__ . "/../config/db.php");

function creerCommande(int $userId, int $prodId, int $quantity, PDO $pdo)
{
    $req = "INSERT INTO orders (usId, proId, quantity, status) VALUES (:usId, :proId, :quantity, 'en_attente')";
    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':usId' => $userId,
        ':proId' => $prodId,
        ':quantity' => $quantity
    ]);
}


function getCommandesByUser(int $userId, PDO $pdo)
{
    $req = "SELECT orders.oId, orders.quantity, orders.status,
                   product.proId, product.proName, product.price
            FROM orders
            JOIN product ON orders.proId = product.proId
            WHERE orders.usId = :usId
            ORDER BY orders.oId DESC";

    $stmt = $pdo->prepare($req);
    $stmt->execute([':usId' => $userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getToutesLesCommandes(PDO $pdo)
{
    $req = "SELECT orders.oId, orders.quantity, orders.status,
                   product.proName, product.price,
                   users.lastName AS clientNom,
                   users.firstname AS clientPrenom
            FROM orders
            JOIN product ON orders.proId = product.proId
            JOIN users ON orders.usId = users.usId
            ORDER BY orders.oId DESC";

    $stmt = $pdo->query($req);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function mettreAJourStatutCommande(int $orderId, string $statut, PDO $pdo)
{
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE oId = :oId");
    return $stmt->execute([':status' => $statut, ':oId' => $orderId]);
}

function getCountCommandes(PDO $pdo)
{
    return $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
}