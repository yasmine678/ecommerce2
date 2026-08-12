<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../order/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit();
}

$commandes = getToutesLesCommandes($pdo);

$statutsDisponibles = [
    'en_attente' => 'En attente',
    'en_cours' => 'En cours',
    'expediee' => 'Expédiée',
    'livee' => 'Livrée',
    'annulee' => 'Annulée',
];

$statutsBadge = [
    'en_attente' => 'bg-secondary',
    'en_cours' => 'bg-info text-dark',
    'expediee' => 'bg-primary',
    'livee' => 'bg-success',
    'annulee' => 'bg-danger',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des commandes - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>
<body>
    <?php include(__DIR__ . "/../admin/sidebar_admin.php"); ?>

    <main class="admin-main container-fluid px-4 my-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h2 class="page-title m-0">Historique des commandes</h2>
            <div class="btn-group" role="group">
                <a href="orders.php" class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart3" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 10H4a.5.5 0 0 1-.485-.379L1.61 2H.5a.5.5 0 0 1-.5-.5M3.14 4l.5 2H4a.5.5 0 0 1 0 1H3.89l.5 2H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.485-.379L2.14 4z" />
                        <path d="M6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0M13 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                    </svg> Commandes
                </a>
                <a href="orders_history.php" class="btn btn-primary d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                        <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z" />
                        <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z" />
                        <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5" />
                    </svg> Historique
                </a>
            </div>
        </div>

        <?php if (isset($_GET['succes'])): ?>
            <div class="alert alert-success">Commande supprimée.</div>
        <?php endif; ?>
        <?php if (isset($_GET['erreur'])): ?>
            <div class="alert alert-danger">Une erreur est survenue. Veuillez réessayer.</div>
        <?php endif; ?>

        <?php if (empty($commandes)): ?>
            <div class="alert alert-info">Aucune commande enregistrée pour le moment.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th>Produit / Service</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>Note client</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $commande): ?>
                            <tr>
                                <td>#<?= $commande['oId'] ?></td>
                                <td><?= htmlspecialchars($commande['clientPrenom'] . ' ' . $commande['clientNom']) ?></td>
                                <td><?= htmlspecialchars($commande['proName']) ?></td>
                                <td><?= $commande['quantity'] ?></td>
                                <td><?= number_format($commande['price'] * $commande['quantity'], 0, ',', ' ') ?> FCFA</td>
                                <td>
                                    <?php if (!empty($commande['note'])): ?>
                                        <span class="text-truncate d-inline-block" style="max-width: 180px;" title="<?= htmlspecialchars($commande['note']) ?>">
                                            <?= htmlspecialchars($commande['note']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $statutsBadge[$commande['status']] ?? 'bg-secondary' ?>">
                                        <?= $statutsDisponibles[$commande['status']] ?? $commande['status'] ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form action="../order/save.php" method="POST"
                                          onsubmit="return confirm('Supprimer définitivement cette commande ?');">
                                        <input type="hidden" name="oId" value="<?= $commande['oId'] ?>">
                                        <input type="hidden" name="redirect" value="admin_history">
                                        <button type="submit" name="validate" value="Supprimer"
                                                class="btn btn-sm btn-outline-danger"
                                                aria-label="Supprimer" title="Supprimer">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L13.882 4zM2.5 3h11V2h-11z" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <script src="../assets/js/bootstrap.js"></script>
</body>
</html>
