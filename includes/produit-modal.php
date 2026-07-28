<!-- Modal détail produit : rempli dynamiquement en JS au clic sur une carte produit -->
<div class="modal fade" id="produitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="produitModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="produitModalCat" class="badge bg-info text-dark bg-opacity-25 border border-info border-opacity-25 rounded-pill mb-2"></span>
                <p id="produitModalDesc" class="text-muted"></p>
                <p class="fw-bold text-warning fs-5" id="produitModalPrice"></p>
            </div>
        </div>
    </div>
</div>
