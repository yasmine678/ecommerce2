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
function getModiPro( int $id, PDO $pdo)
{
    $req = "SELECT * FROM product WHERE proId = :proId";
    $stmt = $pdo->prepare($req);
    $stmt->execute([
        ':proId' => $id
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);

}





function createProd(array $data, PDO $pdo)
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
function deleteProd( int $id, PDO $pdo)
{
    $req = "DELETE FROM product WHERE proId = :proId ";

    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':proId' => $id
    ]);


}
function updateProd(int $id, array $data, PDO $pdo) {
    try {
        // Si une nouvelle image est envoyée, on l'inclut dans la requête SQL
        if (!empty($data['image'])) {
            $sql = "UPDATE product 
                    SET proName = :proName, 
                        prodescription = :prodescription, 
                        price = :price, 
                        catId = :catId, 
                        image = :image 
                    WHERE proId = :proId";
        } else {
            // Sinon, on conserve l'ancienne image
            $sql = "UPDATE product
                    SET proName = :proName, 
                        prodescription = :prodescription, 
                        price = :price, 
                        catId = :catId 
                    WHERE proId = :proId";
        }

        $stmt = $pdo->prepare($sql);

        $params = [
            ':proName'        => $data['proName'],
            ':prodescription' => $data['prodescription'],
            ':price'          => $data['price'],
            ':catId'          => $data['catId'],
            ':proId'          => $id
        ];

        if (!empty($data['image'])) {
            $params[':image'] = $data['image'];
        }

        return $stmt->execute($params);
    } catch (PDOException $e) {
        return false;
    }
}
function getProduitsRecents(PDO $pdo, int $jours = 14)
{
    $req = "SELECT product.*, category.name
            FROM product
            LEFT JOIN category ON product.catId = category.catId
            WHERE product.createdAt >= (NOW() - INTERVAL :jours DAY)
            ORDER BY product.createdAt DESC";

    $stmt = $pdo->prepare($req);
    $stmt->bindValue(':jours', $jours, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCountProduits(PDO $pdo)
{
    $req = "SELECT COUNT(*) FROM product";
    $stmt = $pdo->query($req);
    return $stmt->fetchColumn();
}
function getProduitsPlusVendus(PDO $pdo, $limite = 5)
{
    $req = "SELECT product.proName, SUM(commande_lignes.quantity) AS total_vendu
            FROM commande_lignes
            JOIN commandes ON commande_lignes.cmdId = commandes.cmdId
            JOIN product ON commande_lignes.proId = product.proId
            WHERE commandes.status != 'annulee'
            GROUP BY commande_lignes.proId, product.proName
            ORDER BY total_vendu DESC
            LIMIT :limite";

    $stmt = $pdo->prepare($req);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function uploadProductImage(array $file, string $targetDir): ?string {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5 Mo

    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp  = $file['tmp_name'];

    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Contrôle de l'extension et de la taille
    if (in_array($extension, $allowedExtensions) && $fileSize <= $maxFileSize) {
        // Nom de fichier unique (ex: prod_64f1a2b3c4d5e.webp)
        $newFileName = uniqid('prod_', true) . '.' . $extension;
        $destination = $targetDir . $newFileName;

        // Fonction native PHP corrigée ici :
        if (move_uploaded_file($fileTmp, $destination)) {
            return $newFileName;
        }
    }

    return null;
}


?>