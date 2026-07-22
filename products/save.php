<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../config/db.php");

// Protection : vérifier que le formulaire a bien été soumis via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['validate'])) {

    $action = $_POST['validate'];

    switch ($action) {

        // ==========================================
        // 1. CRÉATION D'UN PRODUIT
        // ==========================================
        case 'Creer':
            $proname        = trim($_POST['proName'] ?? '');
            $prodescription = trim($_POST['prodescription'] ?? '');
            $price          = (float)($_POST['price'] ?? 0);
            $catId          = (int)($_POST['catId'] ?? 0);

            // Gestion de l'image
            $imageName = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageName = uploadProductImage($_FILES['image']);
            }

            // Validation des données obligatoires
            if (!empty($proName) && $price > 0 && $catId > 0) {
                $data = [
                    'proName'        => $proName,
                    'prodescription' => $prodescription,
                    'price'          => $price,
                    'catId'          => $catId,
                    'image'          => $imageName
                ];

                if (createProd($data, $pdo)) {
                    $_SESSION['success'] = "Produit ajouté avec succès !";
                } else {
                    $_SESSION['error'] = "Erreur lors de l'enregistrement en base de données.";
                }
            } else {
                $_SESSION['error'] = "Veuillez remplir tous les champs obligatoires.";
            }
            break;

        // ==========================================
        // 2. MISE À JOUR D'UN PRODUIT
        // ==========================================
        case 'Mise à jour':
            $proid          = (int)($_POST['proid'] ?? 0);
            $proName        = trim($_POST['proName'] ?? '');
            $prodescription = trim($_POST['prodescription'] ?? '');
            $price          = (float)($_POST['price'] ?? 0);
            $catId          = (int)($_POST['catId'] ?? 0);

            if ($proid > 0 && !empty($proname) && $price > 0 && $catId > 0) {
                
                // On prépare le tableau des données de mise à jour
                $data = [
                    'proName'        => $proName,
                    'prodescription' => $prodescription,
                    'price'          => $price,
                    'catId'          => $catId
                ];

                // Si une nouvelle image a été téléversée
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $newImage = uploadProductImage($_FILES['image']);
                    if ($newImage) {
                        $data['image'] = $newImage;
                    }
                }

                if (updateProd($proid, $data, $pdo)) {
                    $_SESSION['success'] = "Produit mis à jour avec succès !";
                } else {
                    $_SESSION['error'] = "Erreur lors de la mise à jour du produit.";
                }
            } else {
                $_SESSION['error'] = "Données invalides ou manquantes pour la modification.";
            }
            break;

        // ==========================================
        // 3. SUPPRESSION D'UN PRODUIT
        // ==========================================
        case 'Supprimer':
            $proid = (int)($_POST['proid'] ?? 0);

            if ($proid > 0) {
                if (deleteProd($proid, $pdo)) {
                    $_SESSION['success'] = "Produit supprimé avec succès !";
                } else {
                    $_SESSION['error'] = "Impossible de supprimer le produit.";
                }
            } else {
                $_SESSION['error'] = "Identifiant de produit non valide.";
            }
            break;

        default:
            $_SESSION['error'] = "Action non reconnue.";
            break;
    }

    // Redirection vers la liste des produits
    header("Location: index_admin.php");
    exit();

} else {
    // Si accès direct sans requête POST
    header("Location: index_admin.php");
    exit();
}

/**
 * Fonction d'assistance pour gérer l'upload des images
 */
function uploadProductImage($file) {
    $uploadDir = __DIR__ . '/../assets/images/';
    
    // Vérification/Création du dossier si nécessaire
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (in_array($fileExtension, $allowedExtensions)) {
        // Nom unique pour éviter les conflits d'images
        $newFileName = uniqid('prod_', true) . '.' . $fileExtension;
        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $newFileName;
        }
    }
    return null;
}