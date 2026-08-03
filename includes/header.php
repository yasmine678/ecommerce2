<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");

$user = $_SESSION['user'] ?? null;
$loggedin = isset($user);
?>

<nav id="mainNavbar" class="navbar navbar-expand-lg navbar-dark fixed-top px-3 px-lg-4" style="background-color: #022f4e;">
    <div class="container-fluid px-0">

        <a href="<?= BASE_URL ?>/index.php" class="navbar-brand p-0">
            <img src="<?= BASE_URL ?>/assets/images/Logo YosiShop.png" class="logo-yosishop" alt="YosiShop">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
            aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav ms-lg-auto align-items-start align-items-lg-center mt-3 mt-lg-0">

                <li class="nav-item my-1 my-lg-0 me-lg-3">
                    <a class="nav-link border border-light rounded px-3 py-2" href="<?= BASE_URL ?>/categories/index.php">
                        Categories
                    </a>
                </li>

                <?php if (!$loggedin): ?>

                    <!-- VISITEUR NON CONNECTE -->
                    <li class="nav-item my-1 my-lg-0 me-lg-3">
                        <a class="nav-link border border-light rounded px-3 py-2" href="<?= BASE_URL ?>/products/index.php">
                            Produits
                        </a>
                    </li>
                    <li class="nav-item my-1 my-lg-0 me-lg-3">
                        <a class="nav-link border border-light rounded px-3 py-2" href="<?= BASE_URL ?>/services/index.php">
                            Services
                        </a>
                    </li>
                    <li class="nav-item my-1 my-lg-0">
                        <a class="nav-link border border-light rounded px-3 py-2" href="<?= BASE_URL ?>/auth/login.php">
                            Connexion
                        </a>
                    </li>

               

                <?php else: ?>

                    <!-- CLIENT CONNECTE -->
                    <li class="nav-item my-1 my-lg-0 me-lg-3">
                        <a class="nav-link border border-light rounded px-3 py-2" href="<?= BASE_URL ?>/products/index.php">
                            Nouveau
                        </a>
                    </li>
                    <li class="nav-item my-1 my-lg-0 me-lg-3">
                        <a class="nav-link border border-light rounded px-3 py-2" href="<?= BASE_URL ?>/services/index.php">
                            Services
                        </a>
                    </li>
                    <li class="nav-item my-1 my-lg-0 me-lg-3">
                        <a class="nav-link border border-light rounded px-3 py-2" href="<?= BASE_URL ?>/cart/index.php">
                            Panier
                        </a>
                    </li>

                    <li class="nav-item my-1 my-lg-0">
                        <a class="nav-link border border-light rounded px-3 py-2" href="<?= BASE_URL ?>/auth/logout.php">
                            Déconnexion
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>

    </div>
</nav>

<script>
    window.BASE_URL = "<?= BASE_URL ?>";

    (function () {
        var nav = document.getElementById("mainNavbar");
        if (!nav) return;

        function setHeaderHeight() {
            document.documentElement.style.setProperty("--header-height", nav.offsetHeight + "px");
        }

        setHeaderHeight();
        window.addEventListener("load", setHeaderHeight);
        window.addEventListener("resize", setHeaderHeight);
        window.addEventListener("orientationchange", setHeaderHeight);

        if (window.ResizeObserver) {
            new ResizeObserver(setHeaderHeight).observe(nav);
        }
    })();
</script>