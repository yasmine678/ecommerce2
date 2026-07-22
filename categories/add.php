<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creation de categories</title>
    <link rel="stylesheet" href="../asset/css/bootstrap.css">
    <link rel="stylesheet" href="../asset/css/style.css">
</head>

<body>
    <form action="./save.php" method="POST">
        <div>
            <label for="name">Nom de categorie</label>
            <input type="text" name="name" id="name"><br><br>
        </div>
        <div>
            <label for="desc">Description</label>
            <input type="text" name="description" id="desc">
        </div>
        <input type="submit" value="Creer" name="validate">

    </form>

</body>

</html>