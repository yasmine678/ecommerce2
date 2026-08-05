<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../auth/controller.php");
 require_once(__DIR__ . "/controller.php") ;
if (!isset($_SESSION['user']['role']) || strtolower(trim($_SESSION['user']['role'])) !== 'manager') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {
    $action = $_POST['validate'] ?? null;

    // Dossier de destination pour les images (doit correspondre au dossier lu par les pages d'affichage)
    $uploadDir = __DIR__ . '/../assets/images/';

    // S'assurer que le dossier existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    
    if ($action === 'Creer') {
        $proName        = trim($_POST['proName'] ?? '');
        $prodescription = trim($_POST['prodescription'] ?? '');
        $price          = floatval($_POST['price'] ?? 0);
        $catId          = intval($_POST['catId'] ?? 0);
        $imageName      = null;

        // Traitement de l'image si téléversée
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = uploadProductImage($_FILES['image'], $uploadDir);
        }

        $stmt = $pdo->prepare("INSERT INTO product (proName, prodescription, price, catId, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$proName, $prodescription, $price, $catId, $imageName]);

        $_SESSION['success'] = "Le produit a été ajouté avec succès !";
    }

   
    elseif ($action === 'mise à jour') {
        $proId          = intval($_POST['proId'] ?? 0);
        $proName        = trim($_POST['proName'] ?? '');
        $prodescription = trim($_POST['prodescription'] ?? '');
        $price          = floatval($_POST['price'] ?? 0);
        $catId          = intval($_POST['catId'] ?? 0);

        // Récupérer l'ancienne image en base de données
        $stmtOld = $pdo->prepare("SELECT image FROM product WHERE proId = ?");
        $stmtOld->execute([$proId]);
        $oldProduct = $stmtOld->fetch(PDO::FETCH_ASSOC);
        $imageName = $oldProduct['image'] ;

        // Si une nouvelle image est téléversée
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Supprimer l'ancienne image sur le disque
            if ($imageName && file_exists($uploadDir . $imageName)) {
                unlink($uploadDir . $imageName);
            }
            // Sauvegarder la nouvelle
            $imageName = uploadProductImage($_FILES['image'], $uploadDir);
        }

        $stmt = $pdo->prepare("UPDATE product SET proName = ?, prodescription = ?, price = ?, catId = ?, image = ? WHERE proId = ?");
        $stmt->execute([$proName, $prodescription, $price, $catId, $imageName, $proId]);

        $_SESSION['success'] = "Le produit a été mis à jour !";
    }

   
    elseif ($action === 'Supprimer') {
        $proId = intval($_POST['proId'] ?? 0);

        // Supprimer le fichier image associé
        $stmtOld = $pdo->prepare("SELECT image FROM product WHERE proId = ?");
        $stmtOld->execute([$proId]);
        $product = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if ($product && !empty($product['image'])) {
            $filePath = $uploadDir . $product['image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM product WHERE proId = ?");
        $stmt->execute([$proId]);

        $_SESSION['success'] = "Le produit a été supprimé !";
    }
}

// Redirection vers la liste des produits
header("Location: index.php");
exit();
