<?php
require_once(__DIR__ . "/../config/db.php");

function getUserByEmail(string $email, PDO $pdo)
{
    $req = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getUserByID(int $id, PDO $pdo)
{
    $req = "SELECT * FROM users WHERE usid = :id";
    $stmt = $pdo->prepare($req);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createUser(array $data, PDO $pdo)
{
    $req = "INSERT INTO users (lastName, firstName, email, password, role) 
            VALUES (:nom, :prenom, :email, :password, :role)";
    $stmt = $pdo->prepare($req);
    return $stmt->execute([
        ':nom' => $data['nom'],
        ':prenom' => $data['prenom'],
        ':email' => $data['email'],
        ':password' => $data['password'],
        ':role' => $data['role']
    ]);
}

function getCountClients(PDO $pdo)
{
    $req = "SELECT COUNT(*) FROM users WHERE role = 'client'";
    $stmt = $pdo->query($req);
    return $stmt->fetchColumn();
}