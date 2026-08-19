<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../config/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Acces refuse']);
    exit();
}

$commandes = getCommandesByUser($_SESSION['user']['usId'], $pdo);

$resultat = array_map(function ($c) {
    return [
        'orderId' => (int) $c['orderId'],
        'status' => $c['status'],
    ];
}, $commandes);

echo json_encode(['commandes' => $resultat]);
