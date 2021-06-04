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

//DEBUT Traitement suppression de ce sujet
if(isset($_GET['suppression']) AND ($_GET['suppression'] == 1) AND isset($_GET['editSujet']) AND !empty($_GET['editSujet']) AND isset($_GET['idProjet']) AND !empty($_GET['idProjet'])){
    $edit_id = htmlspecialchars($_GET['editSujet']);
    $get_idProjet = htmlspecialchars($_GET['idProjet']);
    
    $supprimerSujet = $bdd -> prepare('DELETE FROM listesujets WHERE idProjet= ?  AND idSujet=?');
    $supprimerSujet -> execute(array($get_idProjet,$edit_id));

    $supprimerMatch = $bdd -> prepare('DELETE FROM matchprojet WHERE idProjet= ?  AND idSujet=?');
    $supprimerMatch -> execute(array($get_idProjet,$edit_id));

    echo 'Le sujet a bien été supprimé.';
//FIN Traitement suppression de ce sujet

}else{

    //DEBUT Traitement modification de ce sujet
    if(isset($_GET['editSujet']) AND !empty($_GET['editSujet']) AND isset($_GET['idProjet']) AND !empty($_GET['idProjet'])){
        $verifierSujet = $bdd -> prepare('SELECT * FROM listesujets WHERE idProjet= ? AND idSujet=?');
        $verifierSujet -> execute(array($_GET['idProjet'],$_GET['editSujet']));
        if($verifierSujet -> rowCount() == 1){
            $verifierSujet = $verifierSujet -> fetch();
            $edit_id = htmlspecialchars($_GET['editSujet']);
            $get_idProjet = htmlspecialchars($_GET['idProjet']);
        if(isset($_POST['validation']) && isset($_POST['valider']) AND $_POST['valider'] == 'oui'){
            $edit_projet = $bdd -> prepare('UPDATE listesujets SET nomSujet=?,effMin=?,effMax=?,dateSoutenance=?,lieuSoutenance=?,dateRendu=? WHERE idProjet = "'.$get_idProjet.'" AND idSujet='.$edit_id);
            $edit_projet -> execute(array($_POST["newName"],$_POST["newEffMin"],$_POST["newEffMax"],$_POST["newDateSoutenance"],$_POST["newLieuSoutenance"],$_POST["newDateRendu"]));
            $messageModifSujet = 'Le sujet a bien été modifié.';
            $messageMail = 'Des informations concernant un sujet auquel vous êtes inscrit(e) ont été modifiées.';
            $subject = $verifierSujet['nomSujet'];
            $listeEtudiants = $bdd -> prepare('SELECT * FROM listeutilisateurs NATURAL JOIN matchprojet WHERE idSujet = ?');
            $listeEtudiants -> execute(array($edit_id));
            while($e = $listeEtudiants -> fetch()){
                $mailEtudiant = $e['email'];
                echo $mailEtudiant;
                if(mail($mailEtudiant,$subject,$messageMail)){
                    echo 'mail(s) envoyé(s)';
                }else{
                    echo 'erreur lors de l\'envoi du mail';
                }
            }
        }else{
            $messageModifSujet = 'N\'oubliez pas de confirmer vos changements.';
        }
        }
    }else{
        die('Ce sujet n\'existe pas.');
    }
    //FIN Traitement modification de ce sujet

//DEBUT Traitement descriptif 
   if(!empty($_FILES)){
       $fileName = $_FILES['desc']['name'];
       $fileNameTMP = $_FILES['desc']['tmp_name'];
       $fileExtension = strrchr($fileName,'.');
       $extensions_autorisees = array('.pdf','.PDF');
       $destination = "descriptifsSujets/".$fileName;
       if(in_array($fileExtension,$extensions_autorisees)){
            if(move_uploaded_file($fileNameTMP,$destination)){
                $descBDD = $bdd -> prepare('UPDATE listesujets SET descriptif = ? WHERE idProjet = ? AND idSujet = ?');
                $descBDD -> execute(array($destination,$_GET['idProjet'],$_GET['editSujet']));
                echo 'Fichier envoyé avec succès.';
            }else{
                echo 'Erreur lors de l\'envoi du fichier.';
            }
       }else{
           echo 'Erreur : Le fichier envoyé n\'est pas un PDF.';
       }
   }
//FIN Traitement descriptif 
 
}

?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<head>
    <title> Modifier ce Sujet </title>
    <link rel="stylesheet" type="text/css" href="page.css">
</head>
<body> 
   
<?php if(!(isset($_GET['suppression']) AND ($_GET['suppression'] == 1))){ ?>
        <br/>
        <h1> Modifier ce Sujet <h1> 
            <br/><br/>
        <form method="POST" >
        <p> Nom du Sujet : <input type="text" name="newName" placeholder = "Nouveau Nom" <?php if(isset($verifierSujet['nomSujet'])){ ?> value = <?php echo $verifierSujet['nomSujet'] ; } ?> />  
            <br>
            Effectif Min : <input type="number" name="newEffMin" <?php if(isset($verifierSujet['effMin'])){ ?> value = <?php echo $verifierSujet['effMin'] ;  } ?> />  
            Effectif Max : <input type="number" name="newEffMax" <?php if(isset($verifierSujet['effMax'])){ ?> value = <?php echo $verifierSujet['effMax'] ; } ?> />  
            <br>
            Date Soutenance : <input type="date" name="newDateSoutenance" <?php if(isset($verifierSujet['dateSoutenance'])){ ?> value = <?php echo $verifierSujet['dateSoutenance'] ; } ?> />  
            Lieu Soutenance : <input type="text" name="newLieuSoutenance" placeholder = "Nouveau Lieu" <?php if(isset($verifierSujet['lieuSoutenance'])){ ?> value = <?php echo $verifierSujet['lieuSoutenance'] ; } ?> />  
            Date Rendu : <input type="date" name="newDateRendu" <?php if(isset($verifierSujet['dateRendu'])){ ?> value = <?php echo $verifierSujet['dateRendu'] ; } ?> />  </p>
            Confirmer les Changements : <input type="checkbox" name="valider" value="oui" />
        <br> 
        <button type="submit" name="validation"> <em>Valider</em> </button>
        <button type="reset" name="annuler"> <em>Réinitialiser</em> </button>
        </form>
        <br/>
        <form method= "POST" enctype="multipart/form-data">
            <p> Importer un Descriptif :
           <?php // <input name = "MAX_FILE_SIZE" type="hidden" value="500000" /> ?>
            <input type="file" name = "desc" size = "500000" />
            <input type="submit" name = "validerDescriptif"  />
            </p>
        </form>
        
    <?php } ?>
      <br>  
    <?php if(isset($messageModifSujet)){ echo $messageModifSujet ; } ?>
    <br>
    <?php if(isset($messageTransfert)){ echo $messageTransfert ; } ?>
    <br>

    <a href='deconnexion.php'> Déconnexion</a>
    <a href="projetAdmin.php?idProjet=<?=$get_idProjet?>" > Retour </a>
   
   

</body>
</html>