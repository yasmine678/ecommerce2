<?php
require_once("../categories/controller.php");


$categories = getAll($pdo);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <form action="./save.php" method="POST">
        <div>
            <label for="name">Nom du produit</label>
            <input type="text" name="proname" id="name"><br><br>
        </div>
        <div>
            <label for="desc">Description</label>
            <input type="text" name="prodescription" id="desc">
        </div>
        <div>
            <label for="price">Prix</label>
            <input type="decimal" name="price" id="price" step="0.01">
        </div>
    <div>
    <label for="catId">Catégorie</label>

    <select name="catId" id="catId">
        <option value="">-- Sélectionner une catégorie --</option>

        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['catId'] ?>">
                <?= htmlspecialchars($category['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
        <input type="submit" value="Creer" name="validate">

    </form>

</body>

</html>