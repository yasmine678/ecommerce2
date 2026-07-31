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

// ===== AJOUT AU PANIER SANS QUITTER LA PAGE =====
function afficherMessagePanier(texte, erreur) {
    const div = document.createElement("div");
    div.className = "alert " + (erreur ? "alert-danger" : "alert-success") + " position-fixed shadow";
    div.style.cssText = "top: 100px; right: 20px; z-index: 2000; min-width: 250px;";
    div.textContent = texte;
    document.body.appendChild(div);
    setTimeout(function () {
        div.remove();
    }, 2500);
}

document.querySelectorAll(".ajout-panier").forEach(function (form) {
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        fetch(form.action, {
            method: "POST",
            body: new FormData(form)
        })
            .then(function (response) {
                if (response.url.indexOf("/auth/login.php") !== -1) {
                    window.location.href = "/ecommerce/auth/login.php";
                    return;
                }
                afficherMessagePanier("Produit ajouté au panier ✅");
            })
            .catch(function () {
                afficherMessagePanier("Erreur lors de l'ajout au panier.", true);
            });
    });
});
