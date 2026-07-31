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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="">

    <div class="container my-5" style="max-width: 450px;">
        <h2 class="mb-4">Créer un compte</h2>

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

        <form action="../users/save.php" method="POST" class="row g-3 border border-gray p-4 rounded">
            <div class="col-sm-10">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" autocomplete="family-name" class="form-control" required>
            </div>
            <div class="col-sm-10">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" autocomplete="given-name" class="form-control" required>
            </div>
            <div class="col-sm-10">
                <label class="form-label">Email</label>
                <input type="email" name="email" autocomplete="username" class="form-control" required>
            </div>
            <div class="col-sm-10">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" autocomplete="new-password" class="form-control" required minlength="6">
            </div>
            <div class="col-sm-10">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="password_confirm" autocomplete="new-password" class="form-control" required>
            </div><br>
            <div>
            <button type="submit" name="validate" value="Inscription" class="btn btn-primary w-100">
                Créer mon compte
            </button>
            </div>
        </form>

        <p class="mt-3 text-center">
            Déjà un compte ? <a href="./login.php">Se connecter</a>
        </p>
    </div>

   <script src="../assets/js/bootstrap.js"></script>
</body>
</html>