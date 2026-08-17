<?php
require_once(__DIR__ . "/../config/db.php");

function getUserByEmail(string $email, PDO $pdo)
{
    $req = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function createUser(array $data, PDO $pdo)
{
    $req = "INSERT INTO users (lastName, firstName, email, password, role, profil)
            VALUES (:nom, :prenom, :email, :password, :role, :profil)";
    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':nom' => $data['nom'],
        ':prenom' => $data['prenom'],
        ':email' => $data['email'],
        ':password' => $data['password'],
        ':role' => $data['role'],
        ':profil' => $data['profil'] ?? null
    ]);
}

function getCountClients(PDO $pdo)
{
    $req = "SELECT COUNT(*) FROM users WHERE role = 'client'";
    $stmt = $pdo->query($req);
    return $stmt->fetchColumn();
}