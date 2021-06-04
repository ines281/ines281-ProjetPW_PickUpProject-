<?php
  // Init session
  session_start();
    include 'serveur.php';
  // Validate login
  if(!isset($_SESSION['utilisateur']) || empty($_SESSION['utilisateur']) || !isset($_SESSION['id']) || empty($_SESSION['id']) ){
    header('location: connexion.php');
    exit;
  }

//DEBUT Traitement réservé à la connexion à la base de données
require 'db-config.php';
try{
    $options =
    [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASS, $options);
}
catch(PDOException $pe){
    echo 'ERREUR : '.$pe->getMessage();
};
//FIN Traitement réservé à la connexion à la base de données


//DEBUT Traitement suppression de projet
if(isset($_GET['suppression']) && ($_GET['suppression'] == 1) && isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = htmlspecialchars($_GET['edit']);
    $supprimerProjet = $bdd -> prepare('DELETE FROM listeprojets WHERE idProjet= ?');
    $supprimerProjet -> execute(array($edit_id));

    $supprimerSujets = $bdd -> prepare('DELETE FROM listesujets WHERE idProjet= ?');
    $supprimerSujets -> execute(array($edit_id));

    $supprimerMatch = $bdd -> prepare('DELETE FROM matchprojet WHERE idProjet= ?');
    $supprimerMatch -> execute(array($edit_id));

    $messageModifProjet = 'Le projet a bien été supprimé.';

//FIN Traitement suppression de projet
}else{

    //DEBUT Traitement modification de projet 
    if(isset($_GET['edit']) && !empty($_GET['edit'])) {
        $verifierProjet = $bdd -> prepare('SELECT * FROM listeprojets WHERE idProjet= ?');
        $verifierProjet -> execute(array($_GET['edit']));
        if($verifierProjet -> rowCount() == 1){
            if(isset($_POST["newName"])){
                if(!empty($_POST["newName"])){
                    $verifierProjet = $verifierProjet -> fetch();
                    $edit_id = htmlspecialchars($_GET['edit']);
                    $edit_projet = $bdd -> prepare('UPDATE listeprojets SET nomProjet=? WHERE nomProjet = "'.$verifierProjet['nomProjet'].'" AND idProjet=?');
                    $edit_projet -> execute(array($_POST["newName"],$_GET['edit']));
                    $messageModifProjet = 'Le projet a bien été modifié.';
                }
            }
        }else{
            die('Ce projet n\'existe pas.');
        }
    }else{
        die('Ce projet n\'existe pas.');
    }
    //FIN Traitement modification de projet 
}

?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<head>
    <title> Modifier ce Projet </title>
        <link rel="stylesheet" type="text/css" href="page.css">
</head>
<body> 
    
<?php if(!(isset($_GET['suppression']) && ($_GET['suppression'] == 1))){ ?>

    <h1> Modifier ce Projet <h1> 
        <form method="POST">
        <input type="text" name="newName" placeholder="Nouveau Nom" value="<?php $nomProjet?>"/>
        <input type="submit" value="Modifier le Projet"/>
        </form>

<?php } ?>
    
<br/>
    <?php if(isset($messageModifProjet)) echo $messageModifProjet ; ?>
    
<br>
<a href="affichageAdmin.php" > Retour </a>
<a href='deconnexion.php'> Déconnexion</a>


</body>
</html>