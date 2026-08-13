<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../services/controller.php");

$catId = (int) ($_GET['catId'] ?? 0);

if ($catId <= 0) {
    header('Location: ' . BASE_URL . '/categories/index.php');
    exit;
}

$categorie = getModi($catId, $pdo);
$tousLesProduits = getProduitsByCategorie($catId, $pdo);
$tousLesServices = getServicesByCategorie($catId, $pdo);

if (($_GET['format'] ?? '') === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'categorie' => $categorie['name'] ?? '',
        'total' => count($tousLesProduits) + count($tousLesServices),
        'produits' => array_slice($tousLesProduits, 0, 4),
        'services' => array_slice($tousLesServices, 0, 4),
    ]);
    exit;
}

// Accès direct (sans JS) : redirige vers la liste complète des produits de la catégorie.
header('Location: ' . BASE_URL . '/products/index.php?catId=' . $catId);
exit;
