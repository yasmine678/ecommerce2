<?php
require_once("./controller.php");
require_once("../config/db.php");

$id = $_GET['id'];
$category = getModi($id, $pdo);

if (!$category) {
    die("Catégorie inexistante.");
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
        <input type="hidden" name="id" value="<?php echo $category['catId']; ?>">

        <div>
            <label>Nom :</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars ($category['name']); ?>">

        </div>
        <div>
            <label>Description :</label>
            <input type="text" name="description" value="<?php echo htmlspecialchars($category['description']); ?>">
        </div>

        <input type="submit" value="Mise à jour" name="validate">
    </form>

</body>

</html>