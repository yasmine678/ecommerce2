<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/controller.php");

// Protection : vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: /ecommerce/auth/login.php");
    exit();
}

// Vérification de la soumission en POST via le bouton 'action' ou 'validate'
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Supporte à la fois 'action' (du nouveau modal) et 'validate' (de l'ancien formulaire)
    $action = $_POST['action'] ?? $_POST['validate'] ?? '';

    // 1. ACTION : CRÉER (Créer / create)
    if ($action === 'create' || $action === 'Creer') {
        
        $catName = trim($_POST['name'] ?? $_POST['name'] ?? '');
        $catDescription = trim($_POST['description'] ?? $_POST['description'] ?? '');

        if (empty($catName)) {
            $_SESSION['error'] = "Le nom de la catégorie est obligatoire.";
            header("Location: ./index.php");
            exit();
        }

        $data = [
            'name' => $catName,
            'description' => $catDescription
        ];

        if (create($data, $pdo)) {
            $_SESSION['success'] = "La catégorie a été ajoutée avec succès.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la création.";
        }
        header("Location: ./index.php");
        exit();

    // 2. ACTION : MISE À JOUR (update / Mise à jour)
    } elseif ($action === 'mise à jour') {
        
        $id = filter_input(INPUT_POST, 'catId', FILTER_VALIDATE_INT) ?? filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $catName = trim( $_POST['name'] ?? '');
        $catDescription = trim($_POST['description'] ?? $_POST['description'] ?? '');

        if (!$id || empty($catName)) {
            $_SESSION['error'] = "Informations invalides pour la mise à jour.";
            header("Location: ./index.php");
            exit();
        }

        $data = [
            'name' => $catName,
            'description' => $catDescription
        ];

        if (update($id, $data, $pdo)) {
            $_SESSION['success'] = "La catégorie a été mise à jour avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour de la catégorie.";
        }
        header("Location: ./index.php");
        exit();

    // 3. ACTION : SUPPRIMER (Supprimer / delete)
    } elseif ($action === 'delete' || $action === 'Supprimer') {
        
        $id = filter_input(INPUT_POST, 'catId', FILTER_VALIDATE_INT) ?? filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id && delete($id, $pdo)) {
            $_SESSION['success'] = "La catégorie a été supprimée avec succès.";
        } else {
            $_SESSION['error'] = "Impossible de supprimer la catégorie.";
        }
        header("Location: ./index.php");
        exit();

    } else {
        $_SESSION['error'] = "Action non reconnue.";
        header("Location: ./index.php");
        exit();
    }

} else {
    // Redirection directe si la page est appelée sans POST
    header("Location: ./index.php");
    exit();
}