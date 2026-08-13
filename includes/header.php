<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");

$user = $_SESSION['user'] ?? null;
$loggedin = isset($user);

function navActiveSi(string $suffixe): string
{
    return str_ends_with($_SERVER['SCRIPT_NAME'], $suffixe) ? 'active' : '';
}
?>

<nav id="mainNavbar" class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid px-0 d-flex justify-content-between align-items-center">

        <a href="<?= BASE_URL ?>/index.php" class="navbar-brand p-0">
            <img src="<?= BASE_URL ?>/assets/images/Logo YosiShop.png" class="logo-yosishop" alt="YosiShop">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
            aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse flex-grow-0" id="navbarText">
            <ul class="navbar-nav align-items-start align-items-lg-center gap-lg-1">

                <li class="nav-item">
                    <a class="nav-link px-3 py-2 <?= $loggedin ? navActiveSi('/categories/index.php') : '' ?>"
                        href="<?= $loggedin ? BASE_URL . '/categories/index.php' : BASE_URL . '/index.php#categories' ?>">
                        Categories
                    </a>
                </li>

                <?php if (!$loggedin): ?>

                    <!-- VISITEUR NON CONNECTE -->
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2" href="<?= BASE_URL ?>/index.php#produits">
                            Produits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2" href="<?= BASE_URL ?>/index.php#services">
                            Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 <?= navActiveSi('/auth/login.php') ?>" href="<?= BASE_URL ?>/auth/login.php">
                            Connexion
                        </a>
                    </li>



                <?php else: ?>

                    <!-- CLIENT CONNECTE -->
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 <?= navActiveSi('/nouveautes.php') ?>" href="<?= BASE_URL ?>/nouveautes.php">
                            Nouveau
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 <?= navActiveSi('/services/index.php') ?>" href="<?= BASE_URL ?>/services/index.php">
                            Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 <?= navActiveSi('/cart/index.php') ?>" href="<?= BASE_URL ?>/cart/index.php">
                            Panier
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link px-3 py-2" href="<?= BASE_URL ?>/auth/logout.php">
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