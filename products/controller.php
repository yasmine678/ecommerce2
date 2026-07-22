<?PHP

require_once(__DIR__ . "/../config/db.php");

function getAllPro(PDO $pdo)
{
    $req = "SELECT product.*, category.name 
            FROM product 
            LEFT JOIN category ON product.catId = category.catId";

    $stmt = $pdo->prepare($req);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getModiPro($id, PDO $pdo)
{
    $req = "SELECT * FROM product WHERE proId = :proid";
    $stmt = $pdo->prepare($req);
    $stmt->execute([
        ':proid' => $id
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);

}





function createProd($data, PDO $pdo)
{
    $req = "INSERT INTO product(proName, prodescription, price, catId) VALUES (:proName, :prodescription, :price, :catId)";

    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':proName' => $data['proName'],
        ':prodescription' => $data['prodescription'],
        ':price' => $data['price'],
        ':catId' => $data['catId']
    ]);




}
function deleteProd($id, PDO $pdo)
{
    $req = "DELETE FROM product WHERE proId = :proid ";

    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':proid' => $id
    ]);


}
function updateProd($id, $data, $pdo) {
    try {
        // Si une nouvelle image est envoyée, on l'inclut dans la requête SQL
        if (!empty($data['image'])) {
            $sql = "UPDATE products 
                    SET proName = :proName, 
                        prodescription = :prodescription, 
                        price = :price, 
                        catId = :catId, 
                        image = :image 
                    WHERE proId = :proid";
        } else {
            // Sinon, on conserve l'ancienne image
            $sql = "UPDATE products 
                    SET proName = :proName, 
                        prodescription = :prodescription, 
                        price = :price, 
                        catId = :catId 
                    WHERE proId = :proid";
        }

        $stmt = $pdo->prepare($sql);

        $params = [
            ':proName'        => $data['proName'],
            ':prodescription' => $data['prodescription'],
            ':price'          => $data['price'],
            ':catId'          => $data['catId'],
            ':proid'          => $id
        ];

        if (!empty($data['image'])) {
            $params[':image'] = $data['image'];
        }

        return $stmt->execute($params);
    } catch (PDOException $e) {
        return false;
    }
}
function getCountProduits(PDO $pdo)
{
    $req = "SELECT COUNT(*) FROM product";
    $stmt = $pdo->query($req);
    return $stmt->fetchColumn();
}
function getProduitsPlusVendus(PDO $pdo, $limite = 5)
{
    $req = "SELECT product.proName, SUM(orders.quantity) AS total_vendu
            FROM orders
            JOIN product ON orders.proId = product.proId
            WHERE orders.status != 'annulee'
            GROUP BY orders.proId, product.proName
            ORDER BY total_vendu DESC
            LIMIT :limite";

    $stmt = $pdo->prepare($req);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>