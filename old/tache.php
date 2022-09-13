<?php
include ("pdo_class.php");
class Tache
{
    private $pdo;
    private int $id;
    private string $nom;
    private string $statut;
    private int $liste_id;

     public function __construct(){
        try {
            $this->pdo = new PDO('mysql:host=localhost;dbname=gestion_taches', 'root','');
            //Encodage utf8 des caractères spéciaux
            $this->pdo->exec("set names utf8");
            //$this->m_pdo = new PDO("mysql:host=".SERVER.";dbname=".BASE, USER, PASS);
        }
        catch (PDOException $e)
        {       
            echo "Erreur de connexion à la base de données ".$e->getMessage();
            exit();
        }
    } 

    public function setNom(string $v)
    {
        $this->nom=$v;
    }

    public function getNom():string
    {
        return $this->nom;
    }

    public function setStatut(string $v)
    {
        $this->statut=$v;
    }

    public function getStatut():string
    {
        return $this->statut;
    }

    public function setListe_id(int $v)
    {
        $this->liste_id=$v;
    }

    public function getListe_id():int
    {
        return $this->liste_id;
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

    //Ajout d'une tache. Récupére l'id de l'enregistrement créé pour l'affecter à l'objet courant
    public function add()
    {
        try {
            //$pdo = new PDO('mysql:host=localhost;dbname=gestion_taches', 'root', '');
            $statement = $this->pdo->prepare('INSERT INTO taches( nom, statut, liste_id) VALUES (:nom,:statut,:liste_id)');
            $statement->bindValue(':nom',  $this->nom, PDO::PARAM_STR);
            $statement->bindValue(':statut',  $this->statut, PDO::PARAM_STR);
            $statement->bindValue(':liste_id',  $this->liste_id, PDO::PARAM_INT);

            if ($statement->execute()) {
                $this->id= $pdo->lastInsertId();
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
             //$pdo->exec("set names utf8");
             $statement = $this->pdo->prepare('UPDATE taches SET nom=:nom,statut=:statut,liste_id=:liste_id WHERE id=:id');
             $statement->bindValue(':id',  $this->id, PDO::PARAM_INT);
             $statement->bindValue(':nom',  $this->nom, PDO::PARAM_STR);
             $statement->bindValue(':statut',  $this->statut, PDO::PARAM_STR);
             $statement->bindValue(':liste_id',  $this->liste_id, PDO::PARAM_INT);
 
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

    //Suppression d'une tache
    public function del()
    {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=gestion_taches', 'root', '');
            $statement = $pdo->prepare('DELETE FROM taches WHERE id=:id');
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
     function loadProperties(){
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=gestion_taches', 'root', '');
            $statement = $pdo->prepare('SELECT nom,statut,liste_id FROM taches WHERE id= :id');
            $statement->bindValue(':id',  $this->id, PDO::PARAM_INT);

            if ($statement->execute()) {
                while ($tache = $statement->fetch(PDO::FETCH_OBJ)) {
                    $this->nom= $tache->nom;
                    $this->statut= $tache->statut;
                    $this->liste_id =$tache->liste_id;
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