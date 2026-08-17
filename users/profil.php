<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$isAdmin = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'manager';
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - YosiShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>
<body class="<?= $isAdmin ? 'admin' : 'client' ?>">
    <?php if ($isAdmin) {
        include(__DIR__ . "/../admin/sidebar_admin.php");
    } else {
        include(__DIR__ . "/../includes/header.php");
    } ?>

    <main class="<?= $isAdmin ? 'admin-main container-fluid px-4' : 'container below-header' ?> my-4" style="max-width: 560px;">
        <h2 class="mb-4">Mon profil</h2>

        <?php if (isset($_GET['succes'])): ?>
            <div class="alert alert-success">Profil mis à jour.</div>
        <?php endif; ?>
        <?php if (isset($_GET['erreur'])): ?>
            <div class="alert alert-danger">Veuillez remplir tous les champs.</div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-center mb-4">
                    <?php if (!empty($user['profil'])): ?>
                        <img src="../uploads/profils/<?= htmlspecialchars($user['profil']) ?>" alt="Photo de profil"
                             class="rounded-circle" style="width: 96px; height: 96px; object-fit: cover;">
                    <?php else: ?>
                        <span class="rounded-circle bg-secondary bg-opacity-25 text-secondary d-inline-flex align-items-center justify-content-center fw-bold"
                              style="width: 96px; height: 96px; font-size: 2rem;">
                            <?= htmlspecialchars(mb_strtoupper(mb_substr($user['firstName'] ?? '?', 0, 1))) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <form action="../users/save.php" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($user['firstName'] ?? '') ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['lastName'] ?? '') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                            <div class="form-text">L'email ne peut pas être modifié.</div>
                        </div>
                        <div class="col-12">
                            <label for="profil" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
                                </svg>
                                Changer la photo de profil
                            </label>
                            <input type="file" name="profil" id="profil" accept="image/*" class="visually-hidden">
                            <div class="form-text small text-muted mt-1 fichier-nom"></div>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="validate" value="ModifierProfil" class="btn btn-warning w-100 mt-2">
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php if (!$isAdmin) include(__DIR__ . "/../includes/footer.php"); ?>
    <script src="../assets/js/bootstrap.js"></script>
    <?php if (!$isAdmin): ?>
        <script>
            (function () {
                document.addEventListener("change", function (e) {
                    if (!e.target.matches('input[type="file"]')) return;
                    var zoneNom = e.target.parentElement.querySelector(".fichier-nom");
                    if (!zoneNom) return;
                    zoneNom.textContent = e.target.files.length ? e.target.files[0].name : "";
                });
            })();
        </script>
    <?php endif; ?>
</body>
</html>
