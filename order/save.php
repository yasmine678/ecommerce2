<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit();
}

$action = $_POST['validate'] ?? null;

if ($action === 'Mise à jour') {

    $cmdId = intval($_POST['cmdId'] ?? 0);
    $statut = $_POST['status'] ?? '';

    $statutsValides = ['en_attente', 'en_cours', 'expediee', 'livee', 'annulee'];

    if (!$cmdId || !in_array($statut, $statutsValides)) {
        header("Location: ../admin/orders.php?erreur=1");
        exit();
    }

    mettreAJourStatutCommande($cmdId, $statut, $pdo);
    header("Location: ../admin/orders.php?succes=1");
    exit();

} else {
    echo "Formulaire inconnu";
}