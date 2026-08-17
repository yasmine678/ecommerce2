<?php 
function getAllUsers(PDO $pdo)
{
    $req = "SELECT * FROM users ORDER BY usId DESC";
    $stmt = $pdo->query($req);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserById(int $usId, PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE usId = :usId");
    $stmt->execute([':usId' => $usId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUserRole(int $usId, string $role, PDO $pdo)
{
    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE usId = :usId");
    return $stmt->execute([':role' => $role, ':usId' => $usId]);
}

function updateUserProfil(int $usId, string $nom, string $prenom, ?string $profil, PDO $pdo)
{
    if ($profil !== null) {
        $req = "UPDATE users SET lastName = :nom, firstName = :prenom, profil = :profil WHERE usId = :usId";
        $params = [':nom' => $nom, ':prenom' => $prenom, ':profil' => $profil, ':usId' => $usId];
    } else {
        $req = "UPDATE users SET lastName = :nom, firstName = :prenom WHERE usId = :usId";
        $params = [':nom' => $nom, ':prenom' => $prenom, ':usId' => $usId];
    }
    $stmt = $pdo->prepare($req);
    return $stmt->execute($params);
}

function deleteUser(int $usId, PDO $pdo)
{
    $stmt = $pdo->prepare("DELETE FROM users WHERE usId = :usId");
    return $stmt->execute([':usId' => $usId]);
}

?>