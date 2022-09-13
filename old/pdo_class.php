<?php
	require ('_config.inc.php');
	class SPDO {
		public function __construct(){
			try {
				//$pdo = new PDO('mysql:host=localhost;dbname=gestion_taches', 'root','', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
				$pdo = new PDO('mysql:host=localhost;dbname=gestion_taches', 'root','');
				//Encodage utf8 des caractères spéciaux
            	//$pdo->exec("set names utf8 COLLATE utf8mb4_unicode_ci");
				$pdo->exec("set names utf8");
				return $pdo;
				//$this->m_pdo = new PDO("mysql:host=".SERVER.";dbname=".BASE, USER, PASS);
			}
			catch (PDOException $e)
			{       
				echo "Erreur de connexion à la base de données ".$e->getMessage();
				exit();
			}
		}
		
		
	}
?>