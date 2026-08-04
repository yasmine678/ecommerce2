<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../order/controller.php");
require_once(__DIR__ . "/../config/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    http_response_code(403);
    echo json_encode(['error' => 'Acces refuse']);
    exit();
}

$afterId = isset($_GET['after']) ? (int) $_GET['after'] : 0;

$commandes = getNouvellesCommandes($afterId, $pdo);
$lastId = getDerniereCommandeId($pdo);
$nombreCommandesActives = getCountCommandesActives($pdo);

echo json_encode([
    'lastId' => $lastId,
    'nombreCommandesActives' => (int) $nombreCommandesActives,
    'commandes' => $commandes,
]);
