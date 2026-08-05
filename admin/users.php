<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../users/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit();
}

$utilisateurs = getAllUsers($pdo);
$monId = $_SESSION['user']['usId']; // pour empecher de se supprimer/retrograder soi-meme
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>
<body>
    <?php include(__DIR__ . "/../admin/sidebar_admin.php"); ?>

    <main class="container-fluid px-4 my-4" style="margin-left: 250px; width: calc(100% - 250px);">
        <h2 class="page-title mb-4">Gestion des utilisateurs</h2>

        <?php if (isset($_GET['succes'])): ?>
            <div class="alert alert-success">Opération réussie.</div>
        <?php endif; ?>
        <?php if (isset($_GET['erreur'])): ?>
            <div class="alert alert-danger">Une erreur est survenue.</div>
        <?php endif; ?>

        <h5 class="fw-bold mb-3">Tous les utilisateurs</h5>
        <?php if (empty($utilisateurs)): ?>
            <div class="alert alert-info">Aucun utilisateur trouvé.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                           
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $utilisateur): ?>
                            <tr>
                                <td><?= $utilisateur['usId'] ?></td>
                                <td><?= htmlspecialchars(($utilisateur['firstName'] ?? '') . ' ' . ($utilisateur['lastName'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $utilisateur['role'] === 'manager' ? 'primary' : 'secondary' ?>">
                                        <?= htmlspecialchars($utilisateur['role']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <?php if ($utilisateur['usId'] != $monId): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal" data-bs-target="#modalRole<?= $utilisateur['usId'] ?>">
                                            Changer le rôle
                                        </button>

                                        <form action="./save.php" method="POST" class="d-inline"
                                              onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                            <input type="hidden" name="usId" value="<?= $utilisateur['usId'] ?>">
                                            <button type="submit" name="validate" value="Supprimer"
                                                    class="btn btn-sm btn-outline-danger">
                                                Supprimer
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">(vous)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php foreach ($utilisateurs as $utilisateur): ?>
                <?php if ($utilisateur['usId'] != $monId): ?>
                    <div class="modal fade" id="modalRole<?= $utilisateur['usId'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="./save.php" method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            Rôle de <?= htmlspecialchars($utilisateur['firstName'] ?? $utilisateur['email']) ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="usId" value="<?= $utilisateur['usId'] ?>">
                                        <label class="form-label">Rôle :</label>
                                        <select name="role" class="form-select">
                                            <option value="client" <?= $utilisateur['role'] === 'client' ? 'selected' : '' ?>>
                                                Client
                                            </option>
                                            <option value="manager" <?= $utilisateur['role'] === 'manager' ? 'selected' : '' ?>>
                                                Manager
                                            </option>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" name="validate" value="ChangerRole" class="btn btn-primary">
                                            Enregistrer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <script src="../assets/js/bootstrap.js"></script>
</body>
</html>