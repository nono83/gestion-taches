<?php
require_once ('list.php');
require_once ('_config.inc.php');
$liste=new Liste();

switch  ($_POST['action']){
    case 'add':
        $liste->setNom($_POST['nom']);
        $liste->add();
        break;

    case 'update':
        $liste->setId($_POST['listeID']);
        $liste->setNom($_POST['nom']);
        $liste->update();
        break;

    case 'del':
        $liste->setId($_POST['listeID']);
        //suppression des tâches associées à la liste
        $liste->del(false);

        //suppression de la liste
        $liste->del(true);
        
        break;

    case 'listing': 
        try {
            $pdo = new PDO("mysql:host=".SERVER.";dbname=".BASE, USER, '');
            $pdo->exec("set names utf8"); 
            $statement = $pdo->prepare('SELECT * FROM  listes ORDER BY Nom' );
            if ($statement->execute()) {
                $tachearray = array();
                while ($liste = $statement->fetch(PDO::FETCH_ASSOC)) {
                    $listearray[]= $liste;
                     }
            }else {
                $errorInfo = $statement->errorInfo();
                echo 'Message : '.$errorInfo[2];
            } 
        } catch (PDOException $e) {
            echo 'Impossible de se connecter à la base de données';
        }
        
        echo json_encode($listearray);
}

