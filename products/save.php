<?php
require_once("./controller.php");
require_once("../config/db.php");


if (isset($_POST['validate']) && isset($_POST['validate']) !== '') {

    $validate = $_POST['validate'];


    if ($validate == "Creer") {
        $data = ['proName' => $_POST['proname'], 'prodescription' => $_POST['prodescription'], 'price' => $_POST['price'], 'catId' => $_POST['catId']];
        if (createProd($data, $pdo)) {
            // echo " Les lignes sont ajoutées";
            header("Location: index_admin.php");
            exit();
        } else {
            echo " Erreur";
        }
        

    } else if($validate == "Supprimer"){
        $id = $_POST['proid'];
        
        if(deleteProd($id, $pdo)){
            header("Location: index_admin.php");
            exit();
        } else {
            echo " Erreur";
        } 



        } else if ($validate == "Mise à jour") {

        $id = $_POST['proid'];

        $data = [
            'proName' => $_POST['proname'],
            'prodescription' => $_POST['prodescription'],
            'price' => $_POST['price'],
            'catId' => $_POST['catId']
        ];

        if (updateProd($id, $data, $pdo)) {
            header("Location: index_admin.php");
            exit();
        } else {
            echo "Erreur lors de la mise à jour.";
        }
    } else {

        echo "Formulaire inconnu";
    }

}


