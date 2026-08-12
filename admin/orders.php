<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../order/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit();
}

$commandes = getCommandesActives($pdo);

$statutsDisponibles = [
    'en_attente' => 'En attente',
    'en_cours' => 'En cours',
    'expediee' => 'Expédiée',
    'livee' => 'Livrée',
    'annulee' => 'Annulée',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des commandes - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>
<body>
    <?php include(__DIR__ . "/../admin/sidebar_admin.php"); ?>

    <main class="admin-main container-fluid px-4 my-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h2 class="page-title m-0">Commandes en cours</h2>
            <div class="btn-group" role="group">
                <a href="orders.php" class="btn btn-primary d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart3" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 10H4a.5.5 0 0 1-.485-.379L1.61 2H.5a.5.5 0 0 1-.5-.5M3.14 4l.5 2H4a.5.5 0 0 1 0 1H3.89l.5 2H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.485-.379L2.14 4z" />
                        <path d="M6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0M13 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                    </svg> Commandes
                </a>
                <a href="orders_history.php" class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                        <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z" />
                        <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z" />
                        <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5" />
                    </svg> Historique
                </a>
            </div>
        </div>

        <?php if (isset($_GET['succes'])): ?>
            <div class="alert alert-success">Statut mis à jour.</div>
        <?php endif; ?>
        <?php if (isset($_GET['erreur'])): ?>
            <div class="alert alert-danger">Une erreur est survenue. Veuillez réessayer.</div>
        <?php endif; ?>

        <?php if (empty($commandes)): ?>
            <div class="alert alert-info">Aucune commande en cours pour le moment.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                           
                            <th>Client</th>
                            <th>Produit</th>
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
                                    <span class="badge bg-secondary"><?= $statutsDisponibles[$commande['status']] ?? $commande['status'] ?></span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal" data-bs-target="#modalStatut<?= $commande['oId'] ?>"
                                            aria-label="Changer le statut" title="Changer le statut">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.114.168l-.803 2.008a.25.25 0 0 0 .32.32l2.008-.803a.5.5 0 0 0 .168-.115l6.813-6.812z" />
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                        </svg>
                                    </button>
                                    <form action="../order/save.php" method="POST" class="d-inline"
                                          onsubmit="return confirm('Supprimer définitivement cette commande ?');">
                                        <input type="hidden" name="oId" value="<?= $commande['oId'] ?>">
                                        <input type="hidden" name="redirect" value="admin_active">
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

            <?php foreach ($commandes as $commande): ?>
                <div class="modal fade" id="modalStatut<?= $commande['oId'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="../order/save.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Commande #<?= $commande['oId'] ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="oId" value="<?= $commande['oId'] ?>">

                                    <p class="mb-2">
                                        <strong>Client :</strong>
                                        <?= htmlspecialchars($commande['clientPrenom'] . ' ' . $commande['clientNom']) ?>
                                    </p>
                                    <p class="mb-3">
                                        <strong>Produit :</strong> <?= htmlspecialchars($commande['proName']) ?>
                                        (x<?= $commande['quantity'] ?>)
                                    </p>

                                    <?php if (!empty($commande['note'])): ?>
                                        <p class="mb-3">
                                            <strong>Note du client :</strong><br>
                                            <span class="text-muted"><?= nl2br(htmlspecialchars($commande['note'])) ?></span>
                                        </p>
                                    <?php endif; ?>

                                    <label class="form-label">Nouveau statut :</label>
                                    <select name="status" class="form-select">
                                        <?php foreach ($statutsDisponibles as $valeur => $libelle): ?>
                                            <option value="<?= $valeur ?>" <?= ($valeur === $commande['status']) ? 'selected' : '' ?>>
                                                <?= $libelle ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" name="validate" value="Mise à jour" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <script src="../assets/js/bootstrap.js"></script>
</body>
</html>