<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$commandes = getCommandesByUser($_SESSION['user']['usId'], $pdo);

$libelles = [
    'en_attente' => ['label' => 'En attente', 'classe' => 'secondary'],
    'en_cours' => ['label' => 'En cours', 'classe' => 'info'],
    'expediee' => ['label' => 'Expédiée', 'classe' => 'primary'],
    'livee' => ['label' => 'Livrée', 'classe' => 'success'],
    'annulee' => ['label' => 'Annulée', 'classe' => 'danger'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes commandes - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>
<body class="client">
    <?php include(__DIR__ . "/../includes/header.php"); ?>

    <div class="container my-5 below-header">
        <h2 class="mb-4">Mes commandes</h2>

        <?php if (isset($_GET['succes'])): ?>
            <div class="alert alert-success">Votre commande a bien été enregistrée !</div>
        <?php endif; ?>
        <?php if (isset($_GET['erreur'])): ?>
            <div class="alert alert-danger">Une erreur est survenue. Veuillez réessayer.</div>
        <?php endif; ?>

        <?php if (empty($commandes)): ?>
            <p class="text-muted">Vous n'avez pas encore passé de commande.</p>
            <a href="../products/index.php" class="btn btn-warning">Voir les produits</a>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                          
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $commande): ?>
                            <?php $statutInfo = $libelles[$commande['status']] ?? ['label' => $commande['status'], 'classe' => 'secondary']; ?>
                            <tr>

                                <td><?= htmlspecialchars($commande['proName']) ?></td>
                                <td><?= $commande['quantity'] ?></td>
                                <td><?= number_format($commande['price'] * $commande['quantity'], 0, ',', ' ') ?> FCFA</td>
                                <td>
                                    <span class="badge bg-<?= $statutInfo['classe'] ?>"><?= $statutInfo['label'] ?></span>
                                </td>
                                <td class="text-end">
                                    <form action="./save.php" method="POST"
                                          onsubmit="return confirm('Supprimer cette commande de votre historique ?');">
                                        <input type="hidden" name="oId" value="<?= $commande['oId'] ?>">
                                        <input type="hidden" name="redirect" value="client">
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
    </div>

    <?php include(__DIR__ . "/../includes/footer.php"); ?>
    <script src="../assets/js/bootstrap.js"></script>
</body>
</html>