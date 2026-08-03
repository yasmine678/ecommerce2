<?PHP

require_once(__DIR__ . "/../config/db.php");

function getAllServices(PDO $pdo)
{
    $req = "SELECT service.*, category.name
            FROM service
            LEFT JOIN category ON service.catId = category.catId
            ORDER BY service.servId DESC";

    $stmt = $pdo->prepare($req);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getModiService(int $id, PDO $pdo)
{
    $req = "SELECT * FROM service WHERE servId = :servId";
    $stmt = $pdo->prepare($req);
    $stmt->execute([
        ':servId' => $id
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createService(array $data, PDO $pdo)
{
    $req = "INSERT INTO service(servName, description, priceHours, image, disponible, prestataire, catId)
            VALUES (:servName, :description, :priceHours, :image, :disponible, :prestataire, :catId)";

    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':servName' => $data['servName'],
        ':description' => $data['description'],
        ':priceHours' => $data['priceHours'],
        ':image' => $data['image'] ?? null,
        ':disponible' => $data['disponible'],
        ':prestataire' => $data['prestataire'],
        ':catId' => $data['catId']
    ]);
}

function deleteService(int $id, PDO $pdo)
{
    $req = "DELETE FROM service WHERE servId = :servId ";

    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':servId' => $id
    ]);
}

function updateService(int $id, array $data, PDO $pdo)
{
    if (!empty($data['image'])) {
        $req = "UPDATE service
                SET servName = :servName, description = :description, priceHours = :priceHours,
                    disponible = :disponible,
                    prestataire = :prestataire, catId = :catId, image = :image
                WHERE servId = :servId";
    } else {
        $req = "UPDATE service
                SET servName = :servName, description = :description, priceHours = :priceHours,
                    disponible = :disponible,
                    prestataire = :prestataire, catId = :catId
                WHERE servId = :servId";
    }

    $stmt = $pdo->prepare($req);

    $params = [
        ':servName' => $data['servName'],
        ':description' => $data['description'],
        ':priceHours' => $data['priceHours'],
        ':disponible' => $data['disponible'],
        ':prestataire' => $data['prestataire'],
        ':catId' => $data['catId'],
        ':servId' => $id
    ];

    if (!empty($data['image'])) {
        $params[':image'] = $data['image'];
    }

    return $stmt->execute($params);
}

function getCountServices(PDO $pdo)
{
    $req = "SELECT COUNT(*) FROM service";
    $stmt = $pdo->query($req);
    return $stmt->fetchColumn();
}

?>
