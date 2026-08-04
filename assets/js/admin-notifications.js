// Notifie le manager (bureau + badge) quand de nouvelles commandes arrivent.
(function () {
    "use strict";

    var STORAGE_KEY = "yosishop_last_order_id";
    var POLL_INTERVAL_MS = 20000;
    var ENDPOINT = "../admin/notifications.php";
    var BOOTSTRAP_AFTER = 2147483647; // force une premiere reponse "vide" pour ne pas notifier l'historique

    function getLastSeenId() {
        var v = localStorage.getItem(STORAGE_KEY);
        return v === null ? null : parseInt(v, 10);
    }

    function setLastSeenId(id) {
        localStorage.setItem(STORAGE_KEY, String(id));
    }

    function updateBadge(count) {
        var badge = document.getElementById("commandesBadge");
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove("d-none");
        } else {
            badge.classList.add("d-none");
        }
    }

    function showToast(message) {
        var toast = document.createElement("div");
        toast.textContent = message;
        toast.style.cssText =
            "position:fixed;top:16px;right:16px;z-index:2000;" +
            "background:#198754;color:#fff;padding:12px 18px;border-radius:6px;" +
            "box-shadow:0 4px 12px rgba(0,0,0,.25);font-size:14px;max-width:320px;" +
            "opacity:0;transition:opacity .25s ease;";
        document.body.appendChild(toast);
        requestAnimationFrame(function () { toast.style.opacity = "1"; });
        setTimeout(function () {
            toast.style.opacity = "0";
            setTimeout(function () { toast.remove(); }, 300);
        }, 6000);
    }

    function describe(commandes) {
        var nbProduits = commandes.filter(function (c) { return c.type === "produit"; }).length;
        var nbServices = commandes.filter(function (c) { return c.type === "service"; }).length;
        if (commandes.length === 1) {
            var c = commandes[0];
            return (c.type === "produit" ? "Nouvelle commande produit : " : "Nouvelle commande service : ") +
                c.proName + " (x" + c.quantity + ") - " + c.clientPrenom + " " + c.clientNom;
        }
        var parts = [];
        if (nbProduits > 0) parts.push(nbProduits + " produit" + (nbProduits > 1 ? "s" : ""));
        if (nbServices > 0) parts.push(nbServices + " service" + (nbServices > 1 ? "s" : ""));
        return commandes.length + " nouvelles commandes (" + parts.join(", ") + ")";
    }

    function notifyDesktop(message) {
        if (typeof Notification === "undefined" || Notification.permission !== "granted") {
            return;
        }
        var notif = new Notification("YosiShop - Nouvelle commande", {
            body: message,
            icon: "../assets/images/Logo YosiShop.png",
            tag: "yosishop-orders",
        });
        notif.onclick = function () {
            window.focus();
            window.location.href = "../admin/orders.php";
            notif.close();
        };
    }

    function ensurePermissionButton() {
        if (typeof Notification === "undefined" || Notification.permission !== "default") {
            return;
        }
        var btn = document.createElement("button");
        btn.type = "button";
        btn.textContent = "🔔 Activer les notifications";
        btn.className = "btn btn-sm btn-warning";
        btn.style.cssText = "position:fixed;top:12px;right:12px;z-index:2000;";
        btn.addEventListener("click", function () {
            Notification.requestPermission().then(function () {
                btn.remove();
            });
        });
        document.body.appendChild(btn);
    }

    function poll() {
        var lastSeen = getLastSeenId();
        var after = lastSeen === null ? BOOTSTRAP_AFTER : lastSeen;

        fetch(ENDPOINT + "?after=" + encodeURIComponent(after))
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function (data) {
                if (!data) return;

                if (lastSeen === null) {
                    // premiere visite sur ce navigateur : on memorise juste la reference
                    setLastSeenId(data.lastId);
                    updateBadge(data.nombreCommandesActives);
                    return;
                }

                updateBadge(data.nombreCommandesActives);

                if (data.commandes && data.commandes.length > 0) {
                    var message = describe(data.commandes);
                    showToast(message);
                    notifyDesktop(message);
                    setLastSeenId(data.lastId);
                }
            })
            .catch(function () { /* silencieux : reseau/permission, on retentera au prochain cycle */ });
    }

    document.addEventListener("DOMContentLoaded", function () {
        ensurePermissionButton();
        poll();
        setInterval(poll, POLL_INTERVAL_MS);
    });
})();
