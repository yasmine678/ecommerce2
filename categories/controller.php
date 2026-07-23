<?PHP

require_once(__DIR__ . "/../config/db.php");

function getAll(PDO $pdo)
{
    $req = "SELECT * FROM category";

    $stmt = $pdo->prepare($req);
    $stmt->execute();
    $res = $stmt->fetchAll();
    return $res;


}
function getModi(int $id, PDO $pdo)
{
    $req = "SELECT * FROM category WHERE catId = :catId";
    $stmt = $pdo->prepare($req);
    $stmt->execute([
        ':catId' => $id
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);

}
function getProduitsByCategorie(int $id, PDO $pdo)
{
    $req = "SELECT * FROM product WHERE catId = :catId";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':catId' => $id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}





function create(array $data, PDO $pdo)
{
    $req = "INSERT INTO category(name, description) VALUES (:name, :description)";

    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':name' => $data['name'],
        ':description' => $data['description']
    ]);




}
function delete(int $id, PDO $pdo)
{
    $req = "DELETE FROM category WHERE catId = :catId ";

    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':catId' => $id
    ]);


}
function update(int $id, array $data, PDO $pdo)
{
    $req = "UPDATE category SET name = :name, description = :description WHERE catId = :catId";
    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':name' => $data['name'],
        ':description' => $data['description'],
        ':catId' => $id
    ]);
}
function getCountCategories(PDO $pdo)
{
    $req = "SELECT COUNT(*) FROM category";
    $stmt = $pdo->query($req);
    return $stmt->fetchColumn();
}



?>