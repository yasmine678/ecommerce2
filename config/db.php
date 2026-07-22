<?php
$host = "localhost";
$dbname="paecomdb";
$dbuser= "root";
$dbpass= "";



try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo 'connexion ok';
}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}
