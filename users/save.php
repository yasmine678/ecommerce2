<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// require_once("./controller.php");
require_once("../auth/controller.php");
require_once(__DIR__ . "/../order/controller.php");
require_once(__DIR__ . "/../cart/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (isset($_POST['validate']) && $_POST['validate'] !== '') {

    $validate = $_POST['validate'];

    if ($validate == "Connexion") {



        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            header("Location: ../auth/login.php?erreur=champs_vides");
            exit();
        }

        $user = getUserByEmail($email, $pdo);
        // var_dump($user);
        // die();

        if (!$user || !password_verify($password, $user['password'])) {
            header("Location: ../auth/login.php?erreur=identifiants_incorrects");
            exit();
        }

        $_SESSION['user'] = $user;

        if (!empty($_SESSION['pending_cart'])) {
            $pending = $_SESSION['pending_cart'];
            unset($_SESSION['pending_cart']);

            $ligneExistante = getLigneCart($user['usId'], $pending['proId'], $pending['servId'], $pdo);
            if ($ligneExistante) {
                mettreAJourQuantiteCart($ligneExistante['cId'], $ligneExistante['cquantity'] + $pending['quantity'], $pdo);
            } else {
                insererLigneCart($user['usId'], $pending['proId'], $pending['servId'], $pending['quantity'], $pdo);
            }

            header("Location: ../cart/index.php?succes=1");
            exit();
        }

        if ($user["role"] === "manager") {
            $nouvellesCommandes = getCountCommandesActives($pdo) > 0 ? '?nouvelles_commandes=1' : '';
            header("Location: ../admin/dashboard.php" . $nouvellesCommandes);
            exit();
        } else if ($user["role"] === "client") {
            header("Location: ../index.php");
            exit();
        } else {
            header("Location: ../auth/login.php?erreur=role_inconnu");
            exit();
        }
    } else if ($validate == "Inscription") {

        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $passwordConfirm = $_POST['password_confirm'];
        $role = 'client';

        if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
            header("Location: ../auth/register.php?erreur=champs_vides");
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../auth/register.php?erreur=email_invalide");
            exit();
        }

        if (strlen($password) < 6) {
            header("Location: ../auth/register.php?erreur=mdp_court");
            exit();
        }

        if ($password !== $passwordConfirm) {
            header("Location: ../auth/register.php?erreur=mdp_differents");
            exit();
        }

        if (getUserByEmail($email, $pdo)) {
            header("Location: ../auth/register.php?erreur=email_existe");
            exit();
        }

        $data = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
        ];

        if (createUser($data, $pdo)) {
            $user = getUserByEmail($email, $pdo);
            $_SESSION['user'] = $user;

            if ($user['role'] === 'manager') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            header("Location: ../auth/register.php?erreur=inscription_echouee");
            exit();
        }
    } else {
        echo "Formulaire inconnu";
    }
}


$action = $_POST['validate'] ?? null;
$monId = $_SESSION['user']['usId'];

if ($action === 'ChangerRole') {

    $usId = intval($_POST['usId'] ?? 0);
    $role = $_POST['role'] ?? '';

    if (!$usId || !in_array($role, ['client', 'manager']) || $usId == $monId) {
        header("Location: ../admin/users.php?erreur=1");
        exit();
    }

    updateUserRole($usId, $role, $pdo);
    header("Location: ../admin/users.php?succes=1");
    exit();

} else if ($action === 'Supprimer') {

    $usId = intval($_POST['usId'] ?? 0);

    if (!$usId || $usId == $monId) {
        header("Location: ../admin/users.php?erreur=1");
        exit();
    }

    deleteUser($usId, $pdo);
    header("Location: ../admin/users.php?succes=1");
    exit();

} else {
    echo "Formulaire inconnu";
}