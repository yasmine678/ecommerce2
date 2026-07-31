<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../order/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: /ecommerce/auth/login.php");
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des commandes - YosiShop</title>
    <link rel="stylesheet" href="/ecommerce/assets/css/bootstrap.css">
    <link rel="stylesheet" href="/ecommerce/assets/css/style.css">
</head>
<body>
    <?php include(__DIR__ . "/../admin/sidebar_admin.php"); ?>

    <main class="container-fluid px-4 my-4" style="margin-left: 250px; width: calc(100% - 250px);">
        <h2 class="fw-bold mb-4">Gestion des commandes</h2>

        <?php if (isset($_GET['succes'])): ?>
            <div class="alert alert-success">Statut mis à jour.</div>
        <?php endif; ?>
        <?php if (isset($_GET['erreur'])): ?>
            <div class="alert alert-danger">Une erreur est survenue. Veuillez réessayer.</div>
        <?php endif; ?>

        <?php if (empty($commandes)): ?>
            <div class="alert alert-info">Aucune commande pour le moment.</div>
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
                                            data-bs-toggle="modal" data-bs-target="#modalStatut<?= $commande['oId'] ?>">
                                        Changer le statut
                                    </button>
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