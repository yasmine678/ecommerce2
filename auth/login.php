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
    <link rel="stylesheet" href="http://localhost/ecommerce/assets/css/bootstrap.css">
    <link rel="stylesheet" href="http://localhost/ecommerce/assets/css/style.css">
</head>
<body>

    <div class="container my-5" style="max-width: 450px;">
        <h2 class="mb-4">Connexion</h2>

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

        <form action="../users/save.php" method="POST" class="border border-gray p-4 rounded">
            <div class="col-sm-10">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-sm-10">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div><br>
            <div>   
            <button type="submit" name="validate" value="Connexion" class="btn btn-warning w-100">
                Se connecter
            </button>
            </div>
        </form>

        <p class="mt-3 text-center">
            Pas encore de compte ? <a href="./register.php">Créer un compte</a>
        </p>
    </div>

     <script src="http://localhost/ecommerce/assets/js/bootstrap%20.js"></script>
</body>
</html>