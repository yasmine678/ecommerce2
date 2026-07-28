const liste = document.getElementById("listeCategories");
const boutonDroite = document.getElementById("droite");
const boutonGauche = document.getElementById("gauche");

if (boutonDroite && liste) {
    boutonDroite.onclick = function () {
        liste.scrollBy({
            left: 250,
            behavior: "smooth"
        });
    };
}

if (boutonGauche && liste) {
    boutonGauche.onclick = function () {
        liste.scrollBy({
            left: -250,
            behavior: "smooth"
        });
    };
}

// ===== MODAL DETAIL PRODUIT (clic sur une carte produit) =====
const produitModalEl = document.getElementById("produitModal");

if (produitModalEl) {
    const produitModal = new bootstrap.Modal(produitModalEl);
    const modalTitle = document.getElementById("produitModalTitle");
    const modalCat = document.getElementById("produitModalCat");
    const modalDesc = document.getElementById("produitModalDesc");
    const modalPrice = document.getElementById("produitModalPrice");

    function ouvrirProduitModal(carte) {
        modalTitle.textContent = carte.dataset.name;
        modalDesc.textContent = carte.dataset.desc;
        modalPrice.textContent = carte.dataset.price + " FCFA";

        if (carte.dataset.cat) {
            modalCat.textContent = carte.dataset.cat;
            modalCat.style.display = "inline-block";
        } else {
            modalCat.style.display = "none";
        }

        produitModal.show();
    }

    document.querySelectorAll(".produit-clickable").forEach(function (carte) {
        carte.onclick = function () {
            ouvrirProduitModal(carte);
        };
        carte.onkeydown = function (e) {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                ouvrirProduitModal(carte);
            }
        };
    });
}
