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

$libellesMethodePaiement = [
    'carte' => 'Carte bancaire',
    'flooz' => 'Flooz',
    'tmoney' => 'T-Money',
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
            <?php foreach ($commandes as $commande): ?>
                <?php
                $lignes = getLignesCommande($commande['cmdId'], $pdo);
                $total = 0;
                foreach ($lignes as $ligne) {
                    $total += $ligne['price'] * $ligne['quantity'];
                }
                ?>
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <span class="fw-bold">Commande #<?= $commande['cmdId'] ?></span>
                            <span class="text-muted small"> — <?= htmlspecialchars($commande['clientPrenom'] . ' ' . $commande['clientNom']) ?></span>
                            <span class="text-muted small">— <?= date('d/m/Y à H:i', strtotime($commande['createdAt'])) ?></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary"><?= $statutsDisponibles[$commande['status']] ?? $commande['status'] ?></span>
                            <?php if (($commande['paymentStatus'] ?? 'non_payee') === 'payee'): ?>
                                <span class="badge bg-success">
                                    Payée<?= !empty($commande['paymentMethod']) ? ' · ' . htmlspecialchars($libellesMethodePaiement[$commande['paymentMethod']] ?? $commande['paymentMethod']) : '' ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Non payée</span>
                            <?php endif; ?>
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#modalStatut<?= $commande['cmdId'] ?>"
                                    aria-label="Changer le statut" title="Changer le statut">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.114.168l-.803 2.008a.25.25 0 0 0 .32.32l2.008-.803a.5.5 0 0 0 .168-.115l6.813-6.812z" />
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Article</th>
                                    <th>Quantité</th>
                                    <th class="text-end">Sous-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lignes as $ligne): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($ligne['proName'] ?? 'Article supprimé') ?></td>
                                        <td><?= $ligne['quantity'] ?></td>
                                        <td class="text-end"><?= number_format($ligne['price'] * $ligne['quantity'], 0, ',', ' ') ?> FCFA</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end fw-bold">Total :</td>
                                    <td class="text-end fw-bold text-warning"><?= number_format($total, 0, ',', ' ') ?> FCFA</td>
                                </tr>
                            </tfoot>
                        </table>
                        <?php if (!empty($commande['note'])): ?>
                            <div class="px-3 pb-3">
                                <strong class="small">Note du client :</strong>
                                <span class="text-muted small"><?= nl2br(htmlspecialchars($commande['note'])) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="modal fade" id="modalStatut<?= $commande['cmdId'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="../order/save.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Commande #<?= $commande['cmdId'] ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="cmdId" value="<?= $commande['cmdId'] ?>">

                                    <p class="mb-2">
                                        <strong>Client :</strong>
                                        <?= htmlspecialchars($commande['clientPrenom'] . ' ' . $commande['clientNom']) ?>
                                    </p>
                                    <p class="mb-3">
                                        <strong>Articles :</strong>
                                        <?= htmlspecialchars(implode(', ', array_map(function ($l) {
                                            return ($l['proName'] ?? 'Article supprimé') . ' (x' . $l['quantity'] . ')';
                                        }, $lignes))) ?>
                                    </p>

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
