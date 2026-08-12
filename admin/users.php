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

    <main class="admin-main container-fluid px-4 my-4">
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
                                                data-bs-toggle="modal" data-bs-target="#modalRole<?= $utilisateur['usId'] ?>"
                                                aria-label="Changer le rôle" title="Changer le rôle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.114.168l-.803 2.008a.25.25 0 0 0 .32.32l2.008-.803a.5.5 0 0 0 .168-.115l6.813-6.812z" />
                                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                            </svg>
                                        </button>

                                        <form action="./save.php" method="POST" class="d-inline"
                                              onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                            <input type="hidden" name="usId" value="<?= $utilisateur['usId'] ?>">
                                            <button type="submit" name="validate" value="Supprimer"
                                                    class="btn btn-sm btn-outline-danger"
                                                    aria-label="Supprimer" title="Supprimer">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L13.882 4zM2.5 3h11V2h-11z" />
                                                </svg>
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