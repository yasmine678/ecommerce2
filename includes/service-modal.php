<!-- Modal détail service : rempli dynamiquement en JS au clic sur une carte service -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="serviceModalCat" class="badge bg-info text-dark bg-opacity-25 border border-info border-opacity-25 rounded-pill mb-2"></span>
                <p id="serviceModalDesc" class="text-muted"></p>
                <p class="text-muted small" id="serviceModalProvider"></p>
                <p class="fw-bold text-warning fs-5" id="serviceModalPrice"></p>
            </div>
        </div>
    </div>
</div>
