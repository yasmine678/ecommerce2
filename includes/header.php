<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../cart/controller.php");

$user = $_SESSION['user'] ?? null;
$loggedin = isset($user);
$nombreArticlesPanier = $loggedin ? getCartCountByUser($user['usId'], $pdo) : 0;

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

        <form action="<?= BASE_URL ?>/researsh.php" method="GET" class="site-search d-none d-md-flex" autocomplete="off">
            <input type="search" name="q" id="siteSearchInput" class="site-search-input"
                   placeholder="Rechercher un produit, un service..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit" class="site-search-btn" aria-label="Rechercher">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                </svg>
            </button>
            <div class="site-search-suggestions" id="siteSearchSuggestions"></div>
        </form>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
            aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse flex-grow-0" id="navbarText">
            <form action="<?= BASE_URL ?>/researsh.php" method="GET" class="site-search site-search-mobile d-flex d-md-none" autocomplete="off">
                <input type="search" name="q" class="site-search-input"
                       placeholder="Rechercher un produit, un service..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit" class="site-search-btn" aria-label="Rechercher">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                </button>
            </form>
            <ul class="navbar-nav align-items-start align-items-lg-center gap-lg-1">

                <li class="nav-item">
                    <a class="nav-link px-3 py-2 <?= $loggedin ? navActiveSi('/categories/index.php') : '' ?>"
                        href="<?= $loggedin ? BASE_URL . '/categories/index.php' : BASE_URL . '/index.php#categories' ?>">
                        Categories
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3 py-2 <?= navActiveSi('/promotions.php') ?>" href="<?= BASE_URL ?>/promotions.php">
                        Promotion
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
                        <a class="nav-link px-3 py-2 d-flex align-items-center gap-1 <?= navActiveSi('/cart/index.php') ?>" href="<?= BASE_URL ?>/cart/index.php">
                            Panier
                            <span id="panierBadge" class="badge rounded-pill bg-warning text-dark <?= $nombreArticlesPanier > 0 ? '' : 'd-none' ?>"><?= $nombreArticlesPanier ?></span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 <?= navActiveSi('/order/index.php') ?>" href="<?= BASE_URL ?>/order/index.php">
                            Mes commandes
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link px-3 py-2 d-flex align-items-center gap-2 dropdown-toggle" href="#" role="button"
                           id="menuCompteToggle" aria-expanded="false">
                            <?php if (!empty($user['profil'])): ?>
                                <img src="<?= BASE_URL ?>/uploads/profils/<?= htmlspecialchars($user['profil']) ?>" alt=""
                                     class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
                            <?php else: ?>
                                <span class="rounded-circle bg-secondary bg-opacity-25 text-secondary d-inline-flex align-items-center justify-content-center fw-bold"
                                      style="width: 24px; height: 24px; font-size: .7rem;">
                                    <?= htmlspecialchars(mb_strtoupper(mb_substr($user['firstName'] ?? '?', 0, 1))) ?>
                                </span>
                            <?php endif; ?>
                            <?= htmlspecialchars($user['firstName'] ?? 'Mon compte') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item <?= navActiveSi('/users/profil.php') ?>" href="<?= BASE_URL ?>/users/profil.php">
                                    Mon profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>/auth/logout.php">
                                    Déconnexion
                                </a>
                            </li>
                        </ul>
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

    (function () {
        var toggle = document.getElementById("menuCompteToggle");
        var menu = toggle ? toggle.nextElementSibling : null;
        if (!toggle || !menu) return;

        function fermer() {
            menu.classList.remove("show");
            toggle.setAttribute("aria-expanded", "false");
        }
        function ouvrir() {
            menu.classList.add("show");
            toggle.setAttribute("aria-expanded", "true");
        }

        toggle.addEventListener("click", function (e) {
            e.preventDefault();
            menu.classList.contains("show") ? fermer() : ouvrir();
        });

        document.addEventListener("click", function (e) {
            if (!toggle.contains(e.target) && !menu.contains(e.target)) fermer();
        });

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") fermer();
        });
    })();

    (function () {
        var input = document.getElementById("siteSearchInput");
        var boite = document.getElementById("siteSearchSuggestions");
        if (!input || !boite) return;

        var minuteur = null;
        var requeteEnCours = 0;

        function echapperHtml(texte) {
            var div = document.createElement("div");
            div.textContent = texte || "";
            return div.innerHTML;
        }

        function formaterPrix(prix) {
            return Number(prix || 0).toLocaleString("fr-FR") + " FCFA";
        }

        function fermerSuggestions() {
            boite.classList.remove("show");
            boite.innerHTML = "";
        }

        function afficherSuggestions(data, motCle) {
            var produits = (data.produits || []).slice(0, 4);
            var services = (data.services || []).slice(0, 3);

            if (!produits.length && !services.length) {
                boite.innerHTML = '<div class="site-search-empty">Aucun résultat pour « ' + echapperHtml(motCle) + ' »</div>';
                boite.classList.add("show");
                return;
            }

            var html = "";
            produits.forEach(function (p) {
                html += '<a class="site-search-item" href="' + window.BASE_URL + '/products/detail.php?id=' + p.proId + '">'
                    + (p.image ? '<img src="' + window.BASE_URL + '/uploads/products/' + echapperHtml(p.image) + '" alt="">' : '<span class="site-search-item-noimg"></span>')
                    + '<span class="site-search-item-texte"><span class="site-search-item-nom">' + echapperHtml(p.proName) + '</span>'
                    + '<span class="site-search-item-prix">' + formaterPrix(p.price) + '</span></span>'
                    + '</a>';
            });
            services.forEach(function (s) {
                html += '<a class="site-search-item" href="' + window.BASE_URL + '/services/detail.php?id=' + s.servId + '">'
                    + (s.serImage ? '<img src="' + window.BASE_URL + '/uploads/services/' + echapperHtml(s.serImage) + '" alt="">' : '<span class="site-search-item-noimg"></span>')
                    + '<span class="site-search-item-texte"><span class="site-search-item-nom">' + echapperHtml(s.servName) + '</span>'
                    + '<span class="site-search-item-prix">' + formaterPrix(s.priceHours) + '/' + echapperHtml(s.unite || 'heure') + '</span></span>'
                    + '</a>';
            });
            html += '<a class="site-search-voir-tout" href="' + window.BASE_URL + '/researsh.php?q=' + encodeURIComponent(motCle) + '">'
                + 'Voir tous les résultats (' + data.total + ')</a>';

            boite.innerHTML = html;
            boite.classList.add("show");
        }

        input.addEventListener("input", function () {
            var motCle = input.value.trim();
            clearTimeout(minuteur);

            if (motCle.length < 2) {
                fermerSuggestions();
                return;
            }

            minuteur = setTimeout(function () {
                var idRequete = ++requeteEnCours;
                fetch(window.BASE_URL + "/researsh.php?format=json&q=" + encodeURIComponent(motCle))
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (idRequete !== requeteEnCours) return;
                        afficherSuggestions(data, motCle);
                    })
                    .catch(function () { /* silencieux */ });
            }, 300);
        });

        document.addEventListener("click", function (e) {
            if (!input.contains(e.target) && !boite.contains(e.target)) fermerSuggestions();
        });

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") fermerSuggestions();
        });
    })();
</script>

<?php if ($loggedin): ?>
    <script src="<?= BASE_URL ?>/assets/js/client-notifications.js?v=<?= filemtime(__DIR__ . '/../assets/js/client-notifications.js') ?>" defer></script>
<?php endif; ?>