// ===== RÉVÉLATION AU DÉFILEMENT (sections avec la classe .reveal / .reveal-stagger) =====
(function () {
    const cibles = document.querySelectorAll(".reveal, .reveal-stagger");
    if (!cibles.length) return;

    if (!("IntersectionObserver" in window)) {
        cibles.forEach(function (el) { el.classList.add("in-view"); });
        return;
    }

    const observateur = new IntersectionObserver(function (entrees) {
        entrees.forEach(function (entree) {
            if (entree.isIntersecting) {
                entree.target.classList.add("in-view");
                observateur.unobserve(entree.target);
            }
        });
    }, { threshold: 0.15, rootMargin: "0px 0px -60px 0px" });

    cibles.forEach(function (el) { observateur.observe(el); });
})();

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

function baseUrl() {
    return window.BASE_URL || "";
}

function echapperHtml(texte) {
    const div = document.createElement("div");
    div.textContent = texte == null ? "" : String(texte);
    return div.innerHTML;
}

function formaterPrix(prix) {
    return Number(prix || 0).toLocaleString("fr-FR");
}

// ===== MODAL DETAIL PRODUIT (clic sur une carte produit, y compris injectée dynamiquement) =====
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

    document.addEventListener("click", function (e) {
        const carte = e.target.closest(".produit-clickable");
        if (carte) ouvrirProduitModal(carte);
    });

    document.addEventListener("keydown", function (e) {
        if (e.key !== "Enter" && e.key !== " ") return;
        const carte = e.target.closest(".produit-clickable");
        if (carte) {
            e.preventDefault();
            ouvrirProduitModal(carte);
        }
    });
}

// ===== AJOUT AU PANIER SANS QUITTER LA PAGE (délégation : fonctionne aussi pour les cartes injectées) =====
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

document.addEventListener("submit", function (e) {
    const form = e.target.closest(".ajout-panier");
    if (!form) return;

    e.preventDefault();
    fetch(form.action, {
        method: "POST",
        body: new FormData(form)
    })
        .then(function (response) {
            if (response.url.indexOf("/auth/login.php") !== -1) {
                window.location.href = baseUrl() + "/auth/login.php";
                return;
            }
            afficherMessagePanier("Produit ajouté au panier ✅");
        })
        .catch(function () {
            afficherMessagePanier("Erreur lors de l'ajout au panier.", true);
        });
});

// ===== CARTE PRODUIT REUTILISABLE (modales catégorie / recherche) =====
function creerCarteProduit(produit) {
    const image = produit.image ? baseUrl() + "/assets/images/" + encodeURIComponent(produit.image) : "";
    const prix = formaterPrix(produit.price);

    const col = document.createElement("div");
    col.className = "col";
    col.innerHTML =
        '<div class="card h-100 shadow-sm produit-card d-flex flex-column">' +
        '  <div class="produit-clickable" role="button" tabindex="0"' +
        '       data-proid="' + echapperHtml(produit.proId) + '"' +
        '       data-name="' + echapperHtml(produit.proName) + '"' +
        '       data-desc="' + echapperHtml(produit.prodescription) + '"' +
        '       data-price="' + prix + '"' +
        '       data-image="' + echapperHtml(image) + '"' +
        '       data-cat="' + echapperHtml(produit.catName || "") + '">' +
        '    <img src="' + echapperHtml(image) + '" class="card-img-top produit-image" alt="' + echapperHtml(produit.proName) + '">' +
        '    <div class="card-body pb-0">' +
        '      <h6 class="card-title mb-1">' + echapperHtml(produit.proName) + '</h6>' +
        '      <p class="card-text text-muted small mb-0 produit-description">' + echapperHtml(produit.prodescription) + '</p>' +
        '    </div>' +
        '  </div>' +
        '  <div class="card-body pt-2 mt-auto">' +
        '    <div class="d-flex justify-content-between align-items-center">' +
        '      <span class="fw-bold text-warning">' + prix + ' FCFA</span>' +
        '      <form action="' + baseUrl() + '/cart/add.php" method="POST" class="m-0 ajout-panier">' +
        '        <input type="hidden" name="proId" value="' + echapperHtml(produit.proId) + '">' +
        '        <button type="submit" class="btn btn-sm btn-outline-warning">🛒</button>' +
        '      </form>' +
        '    </div>' +
        '  </div>' +
        '</div>';
    return col;
}

// ===== MODAL CATEGORIE : affiche 4 produits de la categorie cliquée =====
const categorieModalEl = document.getElementById("categorieModal");

if (categorieModalEl) {
    const categorieModal = new bootstrap.Modal(categorieModalEl);
    const categorieModalTitle = document.getElementById("categorieModalTitle");
    const categorieModalBody = document.getElementById("categorieModalBody");
    const categorieModalVoirPlus = document.getElementById("categorieModalVoirPlus");

    document.addEventListener("click", function (e) {
        const bouton = e.target.closest(".categorie-trigger");
        if (!bouton) return;

        const catId = bouton.dataset.catid;
        const catName = bouton.dataset.catname || "";

        categorieModalTitle.textContent = catName;
        categorieModalBody.innerHTML = '<p class="text-muted mb-0">Chargement...</p>';
        categorieModalVoirPlus.href = baseUrl() + "/products/index.php?catId=" + encodeURIComponent(catId);
        categorieModal.show();

        fetch(baseUrl() + "/categories/produits.php?format=json&catId=" + encodeURIComponent(catId))
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                categorieModalBody.innerHTML = "";

                if (!data.produits || data.produits.length === 0) {
                    categorieModalBody.innerHTML = '<p class="text-muted mb-0">Aucun produit dans cette catégorie pour le moment.</p>';
                    return;
                }

                data.produits.forEach(function (produit) {
                    categorieModalBody.appendChild(creerCarteProduit(produit));
                });

                categorieModalVoirPlus.style.display = data.total > data.produits.length ? "" : "none";
            })
            .catch(function () {
                categorieModalBody.innerHTML = '<p class="text-danger mb-0">Impossible de charger les produits pour le moment.</p>';
            });
    });
}

// ===== MODAL RECHERCHE : affiche les résultats sans quitter la page =====
const rechercheModalEl = document.getElementById("rechercheModal");

if (rechercheModalEl) {
    const rechercheModal = new bootstrap.Modal(rechercheModalEl);
    const rechercheModalTitle = document.getElementById("rechercheModalTitle");
    const rechercheModalBody = document.getElementById("rechercheModalBody");

    document.querySelectorAll(".recherche-form").forEach(function (form) {
        form.addEventListener("submit", function (e) {
            const champ = form.querySelector('input[name="q"]');
            const motCle = champ ? champ.value.trim() : "";
            if (!motCle) return;

            e.preventDefault();

            rechercheModalTitle.textContent = "Résultats pour « " + motCle + " »";
            rechercheModalBody.innerHTML = '<p class="text-muted mb-0">Recherche en cours...</p>';
            rechercheModal.show();

            fetch(baseUrl() + "/researsh.php?format=json&q=" + encodeURIComponent(motCle))
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    rechercheModalBody.innerHTML = "";
                    rechercheModalTitle.textContent =
                        data.total + (data.total > 1 ? " résultats" : " résultat") + " pour « " + motCle + " »";

                    if (!data.produits || data.produits.length === 0) {
                        rechercheModalBody.innerHTML = '<p class="text-muted mb-0">Aucun produit ne correspond à votre recherche.</p>';
                        return;
                    }

                    data.produits.forEach(function (produit) {
                        rechercheModalBody.appendChild(creerCarteProduit(produit));
                    });
                })
                .catch(function () {
                    rechercheModalBody.innerHTML = '<p class="text-danger mb-0">Impossible de lancer la recherche pour le moment.</p>';
                });
        });
    });
}
