<?php
require_once("./controller.php");
require_once("../config/db.php");
require_once("../categories/controller.php");

$id = $_GET['id'];
$products = getModiPro($id, $pdo);
$categories = getAll($pdo);

if (!$products) {
    die("ce produit ne fait pas parti du stock");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de catégorie</title>
    <link rel="stylesheet" href="../asset/css/bootstrap.css">
    <link rel="stylesheet" href="../asset/css/style.css">
</head>

<body>
    <form action="./save.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $products['proId']; ?>">

        <div>
            <label>Nom :</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars ($products['proName']); ?>">

        </div>
        <div>
            <label>Description :</label>
            <input type="text" name="description" value="<?php echo htmlspecialchars($products['prodescription']); ?>">
        </div>
         <div>
            <label>Prix :</label>
            <input type="decimal" name="price" value="<?php echo htmlspecialchars($products['price']); ?>">
        </div>
         <div>
            <label>Catégorie :</label>
            <select name="catId">
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?php echo $categorie['catId']; ?>"
                        <?php echo ($categorie['catId'] == $products['catId']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categorie['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <input type="submit" value="Mise à jour" name="validate">
    </form>

</body>

</html>