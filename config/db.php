<?php
// Charge les variables du fichier .env (racine du projet) dans l'environnement
$envPath = __DIR__ . '/../.env';
if (is_file($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $ligne) {
        $ligne = trim($ligne);
        if ($ligne === '' || str_starts_with($ligne, '#') || !str_contains($ligne, '=')) {
            continue;
        }
        [$cle, $valeur] = explode('=', $ligne, 2);
        $cle = trim($cle);
        $valeur = trim(trim($valeur), "\"'");
        if (getenv($cle) === false) {
            putenv("$cle=$valeur");
        }
    }
}

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: '';
$dbuser = getenv('DB_USER') ?: 'root';
$dbpass = getenv('DB_PASS') ?: '';

if (!defined('BASE_URL')) {
    $documentRoot = str_replace('\\', '/', rtrim(realpath($_SERVER['DOCUMENT_ROOT']), '/\\'));
    $projectRoot = str_replace('\\', '/', rtrim(realpath(__DIR__ . '/..'), '/\\'));
    define('BASE_URL', substr($projectRoot, strlen($documentRoot)));
}



try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo 'connexion ok';
}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}
