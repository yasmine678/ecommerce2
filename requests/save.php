<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../users/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: /ecommerce/auth/login.php");
    exit();
}

$action = $_POST['validate'] ?? null;

if ($action === 'Demander') {

    $usId = $_SESSION['user']['usId'];

    if ($_SESSION['user']['role'] !== 'client' || getRequestEnCoursPourUser($usId, $pdo)) {
        header("Location: /ecommerce/index.php?erreur=1");
        exit();
    }

    createRequest($usId, $pdo);
    header("Location: /ecommerce/index.php?succes=demande_envoyee");
    exit();

} else if ($action === 'Accepter' || $action === 'Refuser') {

    if ($_SESSION['user']['role'] !== 'manager') {
        header("Location: /ecommerce/auth/login.php");
        exit();
    }

    $reqId = intval($_POST['reqId'] ?? 0);
    $demande = $reqId ? getRequestById($reqId, $pdo) : null;

    if (!$demande || $demande['status'] !== 'pending') {
        header("Location: /ecommerce/admin/users.php?erreur=1");
        exit();
    }

    if ($action === 'Accepter') {
        updateRequestStatus($reqId, 'accepted', $pdo);
        updateUserRole($demande['usId'], 'manager', $pdo);
    } else {
        updateRequestStatus($reqId, 'refused', $pdo);
    }

    header("Location: /ecommerce/admin/users.php?succes=1");
    exit();

} else {
    echo "Formulaire inconnu";
}
