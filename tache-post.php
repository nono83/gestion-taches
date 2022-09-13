<?php
require_once ('tache.php');
require_once ('_config.inc.php');

$tache=new Tache();

switch ($_POST['oper']){
    
	case 'add':
          $tache->setNom($_POST['Tache']);
          $tache->setStatut($_POST['Statut']);
          $tache->setListe_id($_POST['Liste']);
          $tache->add();  
          break;
	 
	 case 'edit':
        $tache->setId($_POST['TacheID']);
        $tache->setNom($_POST['Tache']);
        $tache->setStatut($_POST['Statut']);
        $tache->setListe_id($_POST['Liste']);
        $tache->update();  
        break;
	 
	 case 'del':
        $tache->setId($_POST['id']);
        $tache->del();  
        break;

    case 'listing':
        try {
            //$pdo = new PDO("mysql:host=".SERVER.";dbname=".BASE, USER, '');
            $pdo = new PDO("mysql:host=".SERVER.";dbname=".BASE, USER, '');
            $pdo->exec("set names utf8"); 
            //Clause where de la requête. Si $_POST['listeID']==0 on retourne la liste entière des tâches sinon juste la liste des tâches liées à la l'id de la liste
            $whereClause=$_POST['listeID']==0?'':'where taches.liste_id=:id';
            $statement = $pdo->prepare('SELECT taches.id, taches.nom as tache, taches.statut as statut, listes.nom as liste  FROM taches INNER JOIN listes ON taches.liste_id=listes.id '.$whereClause);
            $statement->bindValue(':id',  $_POST['listeID'], PDO::PARAM_INT);
             $statement->setFetchMode(PDO::FETCH_CLASS, 'taches'); 
            if ($statement->execute()) {
                $tachearray = array();
                while ($tache = $statement->fetch(PDO::FETCH_ASSOC)) {
               /*       echo '<pre>';
                    print_r($tache);
                    echo '</pre>';  */
                    $tachearray[]= $tache;
                     }
            }else {
                $errorInfo = $statement->errorInfo();
                echo 'Message : '.$errorInfo[2];
            } 
        } catch (PDOException $e) {
            echo 'Impossible de se connecter à la base de données';
        }
        
        echo json_encode($tachearray);
}

