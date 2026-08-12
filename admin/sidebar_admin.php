<?php
require_once(__DIR__ . "/../order/controller.php");
require_once(__DIR__ . "/../config/db.php");
$nombreCommandesEnAttente = getCountCommandesActives($pdo);
$pageActuelle = basename($_SERVER['SCRIPT_NAME']);
function navActive(string $fichier, string $pageActuelle): string
{
    return $fichier === $pageActuelle ? 'active' : '';
}
?>
<!-- Bouton d'ouverture de la sidebar (visible uniquement sous 992px) -->
<button type="button" id="adminSidebarToggle" class="admin-toggle" aria-label="Ouvrir le menu" aria-controls="adminSidebar" aria-expanded="false">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
    </svg>
</button>
<div id="adminSidebarBackdrop" class="admin-backdrop"></div>

<div id="adminSidebar" class="responsive d-flex flex-column flex-shrink-0 p-3 text-white bg-primary" style="width: 250px; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000;">

    <hr class="text-secondary">

    <!-- Navigation -->
    <ul class="nav nav-pills flex-column mb-auto gap-1">
        <li class="nav-item">
            <a href="../admin/dashboard.php" class="nav-link text-white d-flex align-items-center gap-2 <?= navActive('dashboard.php', $pageActuelle) ?>">
                <span class="icon-chip icon-chip-light"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.389.389 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.389.389 0 0 0-.029-.518z" />
                        <path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A8 8 0 0 1 0 10m8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3" />
                    </svg></span> Dashboard
            </a>
        </li>
        <li>
            <a href="../categories/index.php" class="nav-link text-white d-flex align-items-center gap-2 <?= str_ends_with($_SERVER['SCRIPT_NAME'], '/categories/index.php') ? 'active' : '' ?>">
                <span class="icon-chip icon-chip-light"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v.64c.57.265.94.876.856 1.546l-.64 5.124A2.5 2.5 0 0 1 12.733 15H3.267a2.5 2.5 0 0 1-2.482-2.19l-.64-5.124A1.5 1.5 0 0 1 1 6.14zM2 6h12v-.5a.5.5 0 0 0-.5-.5H9c-.964 0-1.71-.629-2.174-1.154C6.374 3.334 5.82 3 5.264 3H2.5a.5.5 0 0 0-.5.5zm-.367 1a.5.5 0 0 0-.496.562l.64 5.124A1.5 1.5 0 0 0 3.267 14h9.466a1.5 1.5 0 0 0 1.489-1.314l.64-5.124A.5.5 0 0 0 14.367 7z" />
                    </svg></span> Catégories
            </a>
        </li>
        <li>
            <a href="../products/index.php" class="nav-link text-white d-flex align-items-center gap-2 <?= str_ends_with($_SERVER['SCRIPT_NAME'], '/products/index.php') ? 'active' : '' ?>">
                <span class="icon-chip icon-chip-light"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24ZM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z" />
                    </svg></span> Produits
            </a>
        </li>
        <li>
            <a href="../services/index.php" class="nav-link text-white d-flex align-items-center gap-2 <?= str_ends_with($_SERVER['SCRIPT_NAME'], '/services/index.php') ? 'active' : '' ?>">
                <span class="icon-chip icon-chip-light"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5m1.886 6.914L15 6.978V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V6.978l6.614 1.936a1.5 1.5 0 0 0 .772 0zM1.5 4h13a.5.5 0 0 1 .5.5v1.464L8.129 7.87a.5.5 0 0 1-.258 0L1 5.964V4.5a.5.5 0 0 1 .5-.5" />
                    </svg></span> Services
            </a>
        </li>
        <li>
            <a href="../admin/orders.php" class="nav-link text-white d-flex align-items-center gap-2 <?= navActive('orders.php', $pageActuelle) ?> <?= navActive('orders_history.php', $pageActuelle) ?>">
                <span class="icon-chip icon-chip-light"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 10H4a.5.5 0 0 1-.485-.379L1.61 2H.5a.5.5 0 0 1-.5-.5M3.14 4l.5 2H4a.5.5 0 0 1 0 1H3.89l.5 2H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.485-.379L2.14 4z" />
                        <path d="M6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0M13 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                    </svg></span> Commandes
                <span id="commandesBadge" class="badge bg-warning text-dark ms-auto <?= $nombreCommandesEnAttente > 0 ? '' : 'd-none' ?>"><?= $nombreCommandesEnAttente ?></span>
            </a>
        </li>
        <li>
            <a href="../admin/users.php" class="nav-link text-white d-flex align-items-center gap-2 <?= navActive('users.php', $pageActuelle) ?>">
                <span class="icon-chip icon-chip-light">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                    </svg>
                </span> Utilisateurs
            </a>
        </li>
    </ul>

    <hr class="text-secondary">

    <!-- Déconnexion -->
    <div>
        <a href="../auth/logout.php" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z" />
                <path fill-rule="evenodd" d="M11.354 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.647 2.646a.5.5 0 0 0 .708.708z" />
            </svg> Déconnexion
        </a>
    </div>
</div>

<script src="../assets/js/admin-notifications.js" defer></script>
<script>
    (function () {
        var toggle = document.getElementById("adminSidebarToggle");
        var sidebar = document.getElementById("adminSidebar");
        var backdrop = document.getElementById("adminSidebarBackdrop");
        if (!toggle || !sidebar || !backdrop) return;

        function ouvrir() {
            sidebar.classList.add("show");
            backdrop.classList.add("show");
            toggle.setAttribute("aria-expanded", "true");
            document.body.style.overflow = "hidden";
        }
        function fermer() {
            sidebar.classList.remove("show");
            backdrop.classList.remove("show");
            toggle.setAttribute("aria-expanded", "false");
            document.body.style.overflow = "";
        }

        toggle.addEventListener("click", function () {
            sidebar.classList.contains("show") ? fermer() : ouvrir();
        });
        backdrop.addEventListener("click", fermer);

        window.addEventListener("resize", function () {
            if (window.innerWidth >= 992) fermer();
        });
    })();
</script>