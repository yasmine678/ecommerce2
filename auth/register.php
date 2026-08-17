<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . "/../config/db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
</head>
<body class="auth-page">

    <div class="container my-5" style="max-width: 480px;">
        <div class="auth-card">
            <a href="../index.php">
                <img src="../assets/images/Logo YosiShop.png" alt="YosiShop" class="logo-yosishop">
            </a>
            <h2 class="mb-1">Créer un compte</h2>
            <p class="text-center text-muted small mb-4">Rejoignez YosiShop en quelques secondes</p>

            <?php if (isset($_GET['erreur'])): ?>
                <div class="alert alert-danger">
                    <?php
                    $messages = [
                        'champs_vides' => "Veuillez remplir tous les champs.",
                        'email_invalide' => "Adresse email invalide.",
                        'mdp_court' => "Le mot de passe doit contenir au moins 6 caractères.",
                        'mdp_differents' => "Les mots de passe ne correspondent pas.",
                        'email_existe' => "Cet email est déjà utilisé."
                    ];
                    echo $messages[$_GET['erreur']] ?? "Une erreur est survenue.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="../users/save.php" method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" autocomplete="family-name" class="form-control" required>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" autocomplete="given-name" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" autocomplete="username" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" autocomplete="new-password" class="form-control" required minlength="6">
                </div>
                <div class="col-12">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirm" autocomplete="new-password" class="form-control" required>
                </div>
                <div class="col-12">
                    <label for="profil" class="form-label">Photo de profil (optionnel)</label>
                    <input type="file" name="profil" id="profil" accept="image/*" class="form-control">
                </div>
                <div class="col-12">
                    <button type="submit" name="validate" value="Inscription" class="btn btn-primary w-100 mt-2">
                        Créer mon compte
                    </button>
                </div>
            </form>

            <p class="mt-4 mb-0 text-center small">
                Déjà un compte ? <a href="./login.php" class="fw-semibold text-decoration-none">Se connecter</a>
            </p>
        </div>
    </div>

   <script src="../assets/js/bootstrap.js"></script>
</body>
</html>