<?php
require_once(__DIR__ . "/../config/db.php");

function createRequest(int $usId, PDO $pdo)
{
    $stmt = $pdo->prepare("INSERT INTO manager_requests (usId, status) VALUES (:usId, 'pending')");
    return $stmt->execute([':usId' => $usId]);
}

function getRequestEnCoursPourUser(int $usId, PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM manager_requests WHERE usId = :usId AND status = 'pending'");
    $stmt->execute([':usId' => $usId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPendingRequests(PDO $pdo)
{
    $req = "SELECT manager_requests.*, users.firstName, users.lastName, users.email
            FROM manager_requests
            JOIN users ON manager_requests.usId = users.usId
            WHERE manager_requests.status = 'pending'
            ORDER BY manager_requests.requestedAt ASC";
    $stmt = $pdo->query($req);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRequestById(int $reqId, PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM manager_requests WHERE reqId = :reqId");
    $stmt->execute([':reqId' => $reqId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateRequestStatus(int $reqId, string $status, PDO $pdo)
{
    $stmt = $pdo->prepare("UPDATE manager_requests SET status = :status, processedAt = NOW() WHERE reqId = :reqId");
    return $stmt->execute([':status' => $status, ':reqId' => $reqId]);
}
