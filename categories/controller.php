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
function getModi($id, PDO $pdo)
{
    $req = "SELECT * FROM category WHERE catId = :catid";
    $stmt = $pdo->prepare($req);
    $stmt->execute([
        ':catid' => $id
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);

}
function getProduitsByCategorie($Id, PDO $pdo)
{
    $req = "SELECT * FROM product WHERE catId = :catId";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':catId' => $Id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}





function create($data, PDO $pdo)
{
    $req = "INSERT INTO category(name, description) VALUES (:name, :description)";

    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':name' => $data['name'],
        ':description' => $data['description']
    ]);




}
function delete($id, PDO $pdo)
{
    $req = "DELETE FROM category WHERE catId = :catid ";

    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':catid' => $id
    ]);


}
function update($id, $data, PDO $pdo)
{
    $req = "UPDATE category SET name = :name, description = :description WHERE catId = :catid";
    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':name' => $data['name'],
        ':description' => $data['description'],
        ':catid' => $id
    ]);
}
function getCountCategories(PDO $pdo)
{
    $req = "SELECT COUNT(*) FROM category";
    $stmt = $pdo->query($req);
    return $stmt->fetchColumn();
}



?>