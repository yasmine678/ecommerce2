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
                    <div class="input-group">
                        <input type="password" name="password" id="password" autocomplete="current-password" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword" aria-label="Afficher le mot de passe" title="Afficher le mot de passe" aria-pressed="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                            </svg>
                        </button>
                    </div>
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
    <script>
        (function () {
            var bouton = document.getElementById("togglePassword");
            var champ = document.getElementById("password");
            if (!bouton || !champ) return;

            var iconeOeil = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">' +
                '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />' +
                '<path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />' +
                '</svg>';
            var iconeOeilBarre = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">' +
                '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-1.755.229l1.322 1.323a3.5 3.5 0 0 1 4.38 4.38l1.323 1.322q.128-.114.253-.234l1.02 1.019a12 12 0 0 1-.887.808z" />' +
                '<path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z" />' +
                '<path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A8 8 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z" />' +
                '</svg>';

            bouton.addEventListener("click", function () {
                var visible = champ.type === "text";
                champ.type = visible ? "password" : "text";
                bouton.innerHTML = visible ? iconeOeil : iconeOeilBarre;
                bouton.setAttribute("aria-pressed", visible ? "false" : "true");
                bouton.setAttribute("aria-label", visible ? "Afficher le mot de passe" : "Masquer le mot de passe");
                bouton.setAttribute("title", visible ? "Afficher le mot de passe" : "Masquer le mot de passe");
            });
        })();
    </script>
</body>
</html>