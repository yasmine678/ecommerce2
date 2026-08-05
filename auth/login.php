<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../config/db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>
<body class="auth-page">

    <div class="container my-5" style="max-width: 440px;">
        <div class="auth-card">
            <a href="../index.php">
                <img src="../assets/images/Logo YosiShop.png" alt="YosiShop" class="logo-yosishop">
            </a>
            <h2 class="mb-1">Connexion</h2>
            <p class="text-center text-muted small mb-4">Ravi de vous revoir sur YosiShop</p>

            <?php if (isset($_GET['erreur'])): ?>
                <div class="alert alert-danger">
                    <?php
                    $messages = [
                        'champs_vides' => "Veuillez remplir tous les champs.",
                        'identifiants_incorrects' => "Email ou mot de passe incorrect."
                    ];
                    echo $messages[$_GET['erreur']] ?? "Une erreur est survenue.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="../users/save.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" autocomplete="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" autocomplete="current-password" class="form-control" required>
                </div>
                <button type="submit" name="validate" value="Connexion" class="btn btn-warning w-100 mt-2">
                    Se connecter
                </button>
            </form>

            <p class="mt-4 mb-0 text-center small">
                Pas encore de compte ? <a href="./register.php" class="fw-semibold text-decoration-none">Créer un compte</a>
            </p>
        </div>
    </div>

     <script src="../assets/js/bootstrap.js"></script>
</body>
</html>