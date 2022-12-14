<?php
require_once ("_config.inc.php");
class Liste
{
    private $pdo;
    private int $id;
    private string $nom;

    public function __construct(){
        try {
            //$this->pdo = new PDO('mysql:host=localhost;dbname=gestion_taches', 'root','');
            $this->pdo = new PDO("mysql:host=".SERVER.";dbname=".BASE, USER, PASS);
            //Encodage utf8 des caractères spéciaux
            $this->pdo->exec("set names utf8");
        }
        catch (PDOException $e)
        {       
            echo "Erreur de connexion à la base de données ".$e->getMessage();
            exit();
        }
    } 
  

    public function setId(int $v)
    {
        $this->id=$v;
        $this->loadProperties();
    }

    public function getId():int
    {
        return $this->id;
    }

    public function setNom(string $v)
    {
        $this->nom=$v;
    }

    public function getNom():string
    {
        return $this->nom;
    }

    public function add()
    {
         try {
            //$pdo = new PDO('mysql:host=localhost;dbname=gestion_taches', 'root', '');
            $statement = $this->pdo->prepare('INSERT INTO listes(nom) VALUES (:nom)');
            $statement->bindValue(':nom',  $this->nom, PDO::PARAM_STR);
            if ($statement->execute()) {
                $this->id= $this->pdo->lastInsertId();
                return true;
            } else {
                $errorInfo = $statement->errorInfo();
                echo 'Message : '.$errorInfo[2];
                return false;
        }
        } catch (PDOException $e) {
            echo 'Impossible de se connecter à la base de données';
        } 
    }

     //Mise à jour d'une tache. 
     public function update()
     {
         try {
             //$pdo = new PDO('mysql:host=localhost;dbname=gestion_taches', 'root', '');
             $statement = $this->pdo->prepare('UPDATE listes SET nom=:nom WHERE id=:id');
             $statement->bindValue(':id',  $this->id, PDO::PARAM_INT);
             $statement->bindValue(':nom',  $this->nom, PDO::PARAM_STR);
 
             if ($statement->execute()) {
                 return true;
             } else {
                 $errorInfo = $statement->errorInfo();
                 echo 'Message : '.$errorInfo[2];
                 return false;
         }
         } catch (PDOException $e) {
             echo 'Impossible de se connecter à la base de données';
         }
     }

    //Suppression d'une liste
    public function del(bool $list)
    {
        try {
            $query=($list)?'DELETE FROM listes WHERE id=:id':'DELETE FROM taches WHERE liste_id=:id';
            //$pdo = new PDO('mysql:host=localhost;dbname=gestion_taches', 'root', '');
            //$statement = $pdo->prepare('DELETE FROM listes WHERE id=:id');
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':id',  $this->id, PDO::PARAM_INT);

            if ($statement->execute()) {
                return true;
            } else {
                $errorInfo = $statement->errorInfo();
                echo 'Message : '.$errorInfo[2];
                return false;
        }
        } catch (PDOException $e) {
            echo 'Impossible de se connecter à la base de données';
        }
    }

    //Affecte toutes les propriétés de l'objet 
    private function loadProperties(){
        try {
            //$pdo = new PDO('mysql:host=localhost;dbname=gestion_taches', 'root', '');
            $statement = $this->pdo->prepare('SELECT nom FROM listes WHERE id= :id');
            $statement->bindValue(':id',  $this->id, PDO::PARAM_INT);

            if ($statement->execute()) {
                while ($liste = $statement->fetch(PDO::FETCH_OBJ)) {
                    $this->nom= $liste->nom;
                 }

                return true;
            } else {
                $errorInfo = $statement->errorInfo();
                echo 'Message : '.$errorInfo[2];
                return false;
        }
        } catch (PDOException $e) {
            echo 'Impossible de se connecter à la base de données';
        }
    }

}
?>