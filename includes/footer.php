<?php
require_once(__DIR__ . "/../categories/controller.php");            
$categories = getAll($pdo);
?>

<footer class="bg-light border-top mt-5 py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-4">
                <h6 class="fw-bold mb-3">YosiShop</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-decoration-none text-muted">Qui sommes-nous</a></li>
                    <li><a href="#" class="text-decoration-none text-muted">Contact</a></li>
                    <li><a href="#" class="text-decoration-none text-muted">Blog</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4">
                <h6 class="fw-bold mb-3">Catégories</h6>
                <ul class="list-unstyled">
                    <?php foreach (array_slice($categories, 0, 3) as $cat): ?>
                        <li>
                            <a href="./categories/produits.php?catId=<?php echo $cat['catId']; ?>"
                               class="text-decoration-none text-muted">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-12 col-md-4">
                <h6 class="fw-bold mb-3">Aide</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-decoration-none text-muted">Livraison</a></li>
                    <li><a href="#" class="text-decoration-none text-muted">Retours</a></li>
                    <li><a href="#" class="text-decoration-none text-muted">FAQ</a></li>
                </ul>
            </div>
        </div>
        <hr>
        <p class="text-center text-muted small mb-0">&copy; <?php echo date('Y'); ?> YosiShop</p>
    </div>
</footer>