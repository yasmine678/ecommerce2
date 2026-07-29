<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../users/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: /ecommerce/auth/login.php");
    exit();
}

$action = $_POST['validate'] ?? null;
$monId = $_SESSION['user']['usId'];

if ($action === 'ChangerRole') {

    $usId = intval($_POST['usId'] ?? 0);
    $role = $_POST['role'] ?? '';

    if (!$usId || !in_array($role, ['client', 'manager']) || $usId == $monId) {
        header("Location: /ecommerce/admin/users.php?erreur=1");
        exit();
    }

    updateUserRole($usId, $role, $pdo);
    header("Location: /ecommerce/admin/users.php?succes=1");
    exit();

} else if ($action === 'Supprimer') {

    $usId = intval($_POST['usId'] ?? 0);

    if (!$usId || $usId == $monId) {
        header("Location: /ecommerce/admin/users.php?erreur=1");
        exit();
    }

    deleteUser($usId, $pdo);
    header("Location: /ecommerce/admin/users.php?succes=1");
    exit();

} else {
    header("Location: /ecommerce/admin/users.php?erreur=1");
    exit();
}
