<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once("./controller.php");
require_once("../auth/controller.php");
require_once(__DIR__ . "/../config/db.php");

if (isset($_POST['validate']) && $_POST['validate'] !== '') {

    $validate = $_POST['validate'];

    if ($validate == "Connexion") {



        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            header("Location: /ecommerce/auth/login.php?erreur=champs_vides");
            exit();
        }

        $user = getUserByEmail($email, $pdo);
        // var_dump($user);
        // die();

        if (!$user || !password_verify($password, $user['password'])) {
            header("Location: /ecommerce/auth/login.php?erreur=identifiants_incorrects");
            exit();
        }

        $_SESSION['user'] = $user;

        if ($user["role"] === "manager") {
            header("Location: /ecommerce/admin/dashboard.php");
            exit();
        } else if ($user["role"] === "client") {
            header("Location: /ecommerce/index.php");
            exit();
        } else {
            header("Location: /ecommerce/auth/login.php?erreur=role_inconnu");
            exit();
        }
    } else if ($validate == "Inscription") {

        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $passwordConfirm = $_POST['password_confirm'];
        $role = $_POST['role'] ?? 'client';

        if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
            header("Location: /ecommerce/auth/register.php?erreur=champs_vides");
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: /ecommerce/auth/register.php?erreur=email_invalide");
            exit();
        }

        if (strlen($password) < 6) {
            header("Location: /ecommerce/auth/register.php?erreur=mdp_court");
            exit();
        }

        if ($password !== $passwordConfirm) {
            header("Location: /ecommerce/auth/register.php?erreur=mdp_differents");
            exit();
        }

        if (getUserByEmail($email, $pdo)) {
            header("Location: /ecommerce/auth/register.php?erreur=email_existe");
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
                header("Location: /ecommerce/admin/dashboard.php");
            } else {
                header("Location: /ecommerce/index.php");
            }
            exit();
        } else {
            header("Location: /ecommerce/auth/register.php?erreur=inscription_echouee");
            exit();
        }
    } else {
        echo "Formulaire inconnu";
    }
}
