<?php
require_once(__DIR__ . "/../config/db.php");

function creerCommande(int $userId, array $lignesPanier, PDO $pdo, ?string $note = null, ?string $paymentMethod = null, string $paymentStatus = 'non_payee'): int
{
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO orders (usId, status, paymentStatus, paymentMethod, note)
                            VALUES (:usId, 'en_attente', :paymentStatus, :paymentMethod, :note)");
    $stmt->execute([
        ':usId' => $userId,
        ':paymentStatus' => $paymentStatus,
        ':paymentMethod' => $paymentMethod,
        ':note' => $note,
    ]);
    $orderId = (int) $pdo->lastInsertId();

    $stmtLigne = $pdo->prepare("INSERT INTO order_lines (orderId, proId, servId, quantity)
                                 VALUES (:orderId, :proId, :servId, :quantity)");
    foreach ($lignesPanier as $ligne) {
        $stmtLigne->execute([
            ':orderId' => $orderId,
            ':proId' => $ligne['prodId'],
            ':servId' => $ligne['servId'],
            ':quantity' => $ligne['cquantity'],
        ]);
    }

    $pdo->commit();
    return $orderId;
}

function getCommandesByUser(int $userId, PDO $pdo)
{
    $req = "SELECT * FROM orders WHERE usId = :usId ORDER BY orderId DESC";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':usId' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLignesCommande(int $orderId, PDO $pdo)
{
    $req = "SELECT order_lines.*,
                   COALESCE(product.proName, service.servName) AS proName,
                   COALESCE(
                       CASE WHEN product.enPromotion = 1 THEN product.prixPromo ELSE product.price END,
                       CASE WHEN service.enPromotion = 1 THEN service.prixPromo ELSE service.price END
                   ) AS price
            FROM order_lines
            LEFT JOIN product ON order_lines.proId = product.proId
            LEFT JOIN service ON order_lines.servId = service.servId
            WHERE order_lines.orderId = :orderId";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':orderId' => $orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getToutesLesCommandes(PDO $pdo)
{
    $req = "SELECT orders.*,
                   COALESCE(users.lastName, 'inconnu') AS clientNom,
                   COALESCE(users.firstName, 'Client') AS clientPrenom
            FROM orders
            LEFT JOIN users ON orders.usId = users.usId
            ORDER BY orders.orderId DESC";
    $stmt = $pdo->query($req);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCommandesActives(PDO $pdo)
{
    $req = "SELECT orders.*,
                   COALESCE(users.lastName, 'inconnu') AS clientNom,
                   COALESCE(users.firstName, 'Client') AS clientPrenom
            FROM orders
            LEFT JOIN users ON orders.usId = users.usId
            WHERE orders.status NOT IN ('livee', 'annulee')
            ORDER BY orders.orderId DESC";
    $stmt = $pdo->query($req);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function mettreAJourStatutCommande(int $orderId, string $statut, PDO $pdo)
{
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE orderId = :orderId");
    return $stmt->execute([':status' => $statut, ':orderId' => $orderId]);
}

function getCountCommandes(PDO $pdo)
{
    return $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
}

function getCountCommandesActives(PDO $pdo)
{
    $req = "SELECT COUNT(*) FROM orders WHERE status NOT IN ('livee', 'annulee')";
    return $pdo->query($req)->fetchColumn();
}

function getDerniereCommandeId(PDO $pdo)
{
    return (int) $pdo->query("SELECT COALESCE(MAX(orderId), 0) FROM orders")->fetchColumn();
}

function getNouvellesCommandes(int $afterId, PDO $pdo)
{
    $req = "SELECT orders.orderId,
                   COALESCE(users.firstName, 'Client') AS clientPrenom,
                   COALESCE(users.lastName, 'inconnu') AS clientNom,
                   (SELECT COUNT(*) FROM order_lines WHERE order_lines.orderId = orders.orderId) AS nombreArticles
            FROM orders
            LEFT JOIN users ON orders.usId = users.usId
            WHERE orders.orderId > :afterId
            ORDER BY orders.orderId ASC";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':afterId' => $afterId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
