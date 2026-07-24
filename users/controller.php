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

function deleteUser(int $usId, PDO $pdo)
{
    $stmt = $pdo->prepare("DELETE FROM users WHERE usId = :usId");
    return $stmt->execute([':usId' => $usId]);
}

function getCountClients(PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'client'");
    $stmt->execute();
    return $stmt->fetchColumn();
}

?>