<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../auth/controller.php");
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../products/controller.php");

if (!isset($_SESSION['user']['role']) || strtolower(trim($_SESSION['user']['role'])) !== 'manager') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {
    $action = $_POST['validate'] ?? null;

    // Dossier de destination pour les images
    $uploadDir = __DIR__ . '/../assets/images/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if ($action === 'Creer') {
        $servName     = trim($_POST['servName'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $priceHours   = floatval($_POST['priceHours'] ?? 0);
        $provider    = trim($_POST['provider'] ?? '');
        $available    = ($_POST['available'] ?? '1') === '1';
        $catId        = intval($_POST['catId'] ?? 0);
        $imageName    = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = uploadProductImage($_FILES['image'], $uploadDir);
        }

        createService([
            'servName' => $servName,
            'description' => $description,
            'priceHours' => $priceHours,
            'serImage' => $imageName,
            'available' => $available,
            'provider' => $provider,
            'catId' => $catId,
        ], $pdo);

        $_SESSION['success'] = "Le service a été ajouté avec succès !";
    } elseif ($action === 'mise à jour') {
        $servId       = intval($_POST['servId'] ?? 0);
        $servName     = trim($_POST['servName'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $priceHours   = floatval($_POST['priceHours'] ?? 0);
        $provider    = trim($_POST['provider'] ?? '');
        $available    = ($_POST['available'] ?? '1') === '1';
        $catId        = intval($_POST['catId'] ?? 0);
        $imageName    = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Supprimer l'ancienne image sur le disque
            $ancien = getModiService($servId, $pdo);
            if ($ancien && !empty($ancien['serImage']) && file_exists($uploadDir . $ancien['serImage'])) {
                unlink($uploadDir . $ancien['serImage']);
            }
            $imageName = uploadProductImage($_FILES['image'], $uploadDir);
        }

        updateService($servId, [
            'servName' => $servName,
            'description' => $description,
            'priceHours' => $priceHours,
            'serImage' => $imageName,
            'available' => $available,
            'provider' => $provider,
            'catId' => $catId,
        ], $pdo);

        $_SESSION['success'] = "Le service a été mis à jour !";
    } elseif ($action === 'Supprimer') {
        $servId = intval($_POST['servId'] ?? 0);

        $service = getModiService($servId, $pdo);
        if ($service && !empty($service['serImage'])) {
            $filePath = $uploadDir . $service['serImage'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        deleteService($servId, $pdo);
        $_SESSION['success'] = "Le service a été supprimé !";
    }
}

header("Location: index.php");
exit();
