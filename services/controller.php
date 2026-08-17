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
    $req = "INSERT INTO service(servName, description, priceHours, serImage, available, provider, catId)
            VALUES (:servName, :description, :priceHours, :serImage, :available, :provider, :catId)";

    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':servName' => $data['servName'],
        ':description' => $data['description'],
        ':priceHours' => $data['priceHours'],
        ':serImage' => $data['serImage'] ?? null,
        ':available' => $data['available'] ? 1 : 0,
        ':provider' => $data['provider'],
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
    if (!empty($data['serImage'])) {
        $req = "UPDATE service
                SET servName = :servName, description = :description, priceHours = :priceHours,
                    available = :available,
                    provider = :provider, catId = :catId, serImage = :serImage
                WHERE servId = :servId";
    } else {
        $req = "UPDATE service
                SET servName = :servName, description = :description, priceHours = :priceHours,
                    available = :available,
                    provider = :provider, catId = :catId
                WHERE servId = :servId";
    }

    $stmt = $pdo->prepare($req);

    $params = [
        ':servName' => $data['servName'],
        ':description' => $data['description'],
        ':priceHours' => $data['priceHours'],
        ':available' => $data['available'] ? 1 : 0,
        ':provider' => $data['provider'],
        ':catId' => $data['catId'],
        ':servId' => $id
    ];

    if (!empty($data['serImage'])) {
        $params[':serImage'] = $data['serImage'];
    }

    return $stmt->execute($params);
}

function getServicesRecents(PDO $pdo, int $jours = 14)
{
    $req = "SELECT service.*, category.name
            FROM service
            LEFT JOIN category ON service.catId = category.catId
            WHERE service.createdAt >= (NOW() - INTERVAL :jours DAY)
            ORDER BY service.createdAt DESC";

    $stmt = $pdo->prepare($req);
    $stmt->bindValue(':jours', $jours, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCountServices(PDO $pdo)
{
    $req = "SELECT COUNT(*) FROM service";
    $stmt = $pdo->query($req);
    return $stmt->fetchColumn();
}

function searchServices(string $keyword, PDO $pdo)
{
    $req = "SELECT service.*, category.name AS catName
            FROM service
            LEFT JOIN category ON service.catId = category.catId
            WHERE service.servName LIKE :keyword
               OR service.description LIKE :keyword
               OR category.name LIKE :keyword
            ORDER BY service.servName ASC";

    $stmt = $pdo->prepare($req);
    $stmt->execute([':keyword' => '%' . $keyword . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getServicesLesPlusCommandes(PDO $pdo, int $limite = 8)
{
    $req = "SELECT service.servName AS name, SUM(commande_lignes.quantity) AS nombre
            FROM commande_lignes
            JOIN commandes ON commande_lignes.cmdId = commandes.cmdId
            JOIN service ON commande_lignes.servId = service.servId
            WHERE commandes.status != 'annulee'
            GROUP BY commande_lignes.servId, service.servName
            ORDER BY nombre DESC
            LIMIT :limite";

    $stmt = $pdo->prepare($req);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
