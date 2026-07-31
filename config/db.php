<?php
$host = "localhost";
$dbname="paecomdb";
$dbuser= "root";
$dbpass= "";

if (!defined('BASE_URL')) {
    $documentRoot = str_replace('\\', '/', rtrim(realpath($_SERVER['DOCUMENT_ROOT']), '/\\'));
    $projectRoot = str_replace('\\', '/', rtrim(realpath(__DIR__ . '/..'), '/\\'));
    define('BASE_URL', substr($projectRoot, strlen($documentRoot)));
}



try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo 'connexion ok';
}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}
