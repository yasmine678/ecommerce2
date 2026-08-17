<?php
require_once(__DIR__ . "/../config/db.php");

function creerCommande(int $userId, array $lignesPanier, PDO $pdo, ?string $note = null, ?string $paymentMethod = null, string $paymentStatus = 'non_payee'): int
{
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO commandes (usId, status, paymentStatus, paymentMethod, note)
                            VALUES (:usId, 'en_attente', :paymentStatus, :paymentMethod, :note)");
    $stmt->execute([
        ':usId' => $userId,
        ':paymentStatus' => $paymentStatus,
        ':paymentMethod' => $paymentMethod,
        ':note' => $note,
    ]);
    $cmdId = (int) $pdo->lastInsertId();

    $stmtLigne = $pdo->prepare("INSERT INTO commande_lignes (cmdId, proId, servId, quantity)
                                 VALUES (:cmdId, :proId, :servId, :quantity)");
    foreach ($lignesPanier as $ligne) {
        $stmtLigne->execute([
            ':cmdId' => $cmdId,
            ':proId' => $ligne['prodId'],
            ':servId' => $ligne['servId'],
            ':quantity' => $ligne['cquantity'],
        ]);
    }

    $pdo->commit();
    return $cmdId;
}

function getCommandesByUser(int $userId, PDO $pdo)
{
    $req = "SELECT * FROM commandes WHERE usId = :usId ORDER BY cmdId DESC";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':usId' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLignesCommande(int $cmdId, PDO $pdo)
{
    $req = "SELECT commande_lignes.*,
                   COALESCE(product.proName, service.servName) AS proName,
                   COALESCE(product.price, service.priceHours) AS price
            FROM commande_lignes
            LEFT JOIN product ON commande_lignes.proId = product.proId
            LEFT JOIN service ON commande_lignes.servId = service.servId
            WHERE commande_lignes.cmdId = :cmdId";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':cmdId' => $cmdId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getToutesLesCommandes(PDO $pdo)
{
    $req = "SELECT commandes.*,
                   COALESCE(users.lastName, 'inconnu') AS clientNom,
                   COALESCE(users.firstName, 'Client') AS clientPrenom
            FROM commandes
            LEFT JOIN users ON commandes.usId = users.usId
            ORDER BY commandes.cmdId DESC";
    $stmt = $pdo->query($req);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCommandesActives(PDO $pdo)
{
    $req = "SELECT commandes.*,
                   COALESCE(users.lastName, 'inconnu') AS clientNom,
                   COALESCE(users.firstName, 'Client') AS clientPrenom
            FROM commandes
            LEFT JOIN users ON commandes.usId = users.usId
            WHERE commandes.status NOT IN ('livee', 'annulee')
            ORDER BY commandes.cmdId DESC";
    $stmt = $pdo->query($req);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function mettreAJourStatutCommande(int $cmdId, string $statut, PDO $pdo)
{
    $stmt = $pdo->prepare("UPDATE commandes SET status = :status WHERE cmdId = :cmdId");
    return $stmt->execute([':status' => $statut, ':cmdId' => $cmdId]);
}

function getCountCommandes(PDO $pdo)
{
    return $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
}

function getCountCommandesActives(PDO $pdo)
{
    $req = "SELECT COUNT(*) FROM commandes WHERE status NOT IN ('livee', 'annulee')";
    return $pdo->query($req)->fetchColumn();
}

function getDerniereCommandeId(PDO $pdo)
{
    return (int) $pdo->query("SELECT COALESCE(MAX(cmdId), 0) FROM commandes")->fetchColumn();
}

function getNouvellesCommandes(int $afterId, PDO $pdo)
{
    $req = "SELECT commandes.cmdId,
                   COALESCE(users.firstName, 'Client') AS clientPrenom,
                   COALESCE(users.lastName, 'inconnu') AS clientNom,
                   (SELECT COUNT(*) FROM commande_lignes WHERE commande_lignes.cmdId = commandes.cmdId) AS nombreArticles
            FROM commandes
            LEFT JOIN users ON commandes.usId = users.usId
            WHERE commandes.cmdId > :afterId
            ORDER BY commandes.cmdId ASC";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':afterId' => $afterId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
