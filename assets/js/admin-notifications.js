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
        if (commandes.length === 1) {
            var c = commandes[0];
            return "Nouvelle commande #" + c.cmdId + " (" + c.nombreArticles + " article" + (c.nombreArticles > 1 ? "s" : "") + ") - " +
                c.clientPrenom + " " + c.clientNom;
        }
        return commandes.length + " nouvelles commandes";
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
        btn.innerHTML =
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell-fill" viewBox="0 0 16 16">' +
            '<path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />' +
            '</svg> Activer les notifications';
        btn.className = "btn btn-sm btn-warning d-flex align-items-center gap-2";
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
