<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$role = $_SESSION['user']['role'];
$action = $_POST['validate'] ?? null;

if ($action === 'Mise à jour') {

    if ($role !== 'manager') {
        header("Location: ../auth/login.php");
        exit();
    }

    $orderId = intval($_POST['oId'] ?? 0);
    $statut = $_POST['status'] ?? '';

    $statutsValides = ['en_attente', 'en_cours', 'expediee', 'livee', 'annulee'];

    if (!$orderId || !in_array($statut, $statutsValides)) {
        header("Location: ../admin/orders.php?erreur=1");
        exit();
    }

    mettreAJourStatutCommande($orderId, $statut, $pdo);
    header("Location: ../admin/orders.php?succes=1");
    exit();

} else if ($action === 'Supprimer') {

    $cibles = [
        'client' => '../order/index.php',
        'admin_active' => '../admin/orders.php',
        'admin_history' => '../admin/orders_history.php',
    ];
    $cible = $cibles[$_POST['redirect'] ?? 'client'] ?? $cibles['client'];

    $orderId = intval($_POST['oId'] ?? 0);

    if (!$orderId) {
        header("Location: $cible?erreur=1");
        exit();
    }

    if ($role === 'manager') {
        supprimerCommande($orderId, $pdo);
    } else {
        $commande = getCommandeById($orderId, $pdo);
        if ($commande && (int) $commande['usId'] === (int) $_SESSION['user']['usId']) {
            supprimerCommande($orderId, $pdo);
        } else {
            header("Location: $cible?erreur=1");
            exit();
        }
    }

    header("Location: $cible?succes=1");
    exit();

} else {
    echo "Formulaire inconnu";
}