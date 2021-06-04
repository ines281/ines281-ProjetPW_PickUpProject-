<?php
  // Init session
  session_start();
    include 'serveur.php';
  // Validate login
 if(!isset($_SESSION['utilisateur'])  || !isset($_SESSION['id']) || !isset($_SESSION['idUtilisateur'])  ){
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
  
    $projetsEleve = $bdd->query('SELECT * FROM matchprojet NATURAL JOIN listeprojets WHERE idUtilisateur = '.$_SESSION['idUtilisateur'].'');
}
catch(PDOException $pe){
    echo 'ERREUR : '.$pe->getMessage();
};
//FIN Traitement réservé à la connexion à la base de données


//DEBUT Traitement choix du sujet
if(isset($_POST['choixSujet']) && !empty($_POST['choixSujet'])){
  $tabIDs = explode("|",$_POST['choixSujet']);
  $projetID = $tabIDs[0];
  $sujetID = $tabIDs[1];
  $traitementSujet = $bdd -> prepare('UPDATE matchprojet SET idSujet=? WHERE idProjet=? AND idUtilisateur=?');
  $traitementSujet -> execute(array($sujetID,$projetID,$_SESSION['idUtilisateur'])); 
}
//FIN Traitement choix du sujet

//DEBUT Lien Rendu
if(isset($_POST['nouveauLien']) && !empty($_POST['nouveauLien'])){
  if(isset($_POST['validerNouveauLien']) && !empty($_POST['validerNouveauLien'])){
    $tabIDs2 = explode("|",$_POST['validerNouveauLien']);
    $projetID2 = $tabIDs2[0];
    $sujetID2 = $tabIDs2[1];
    $traitementLien = $bdd -> prepare('UPDATE listesujets SET lienRendu=? WHERE idSujet=? AND idProjet=?');
    $traitementLien -> execute(array($_POST['nouveauLien'],$sujetID2,$projetID2)); 
    $traitementLien2 = $bdd -> prepare('UPDATE listesujets SET dateLien=NOW() WHERE idSujet=? AND idProjet=?');
    $traitementLien2 -> execute(array($sujetID2,$projetID2)); 
  }else{
    echo 'Cochez la case pour valider vos changements';
  }
}
//DEBUT Lien Rendu

?>

<!DOCTYPE html>
<html>
	<head>
		<title> Accueil </title>
    <link rel="stylesheet" type="text/css" href="page.css">
	</head>
	<body>
  <br/>
  <?php if($projetsEleve -> rowCount() < 1){
    echo 'Vous n\'êtes affecté(e) à aucun projet actuellement.' ; 
  } ?>
    <br/><br/>
    <div>
    <form method="POST">
        <p> Afficher les élèves affectés aux sujets ?
        <input type="checkbox" name="afficherElevesSujets" value="oui" /> 
        <button type="submit" value="Valider">Valider </button>
    </form>
    </div>

  <?php while ($projet = $projetsEleve->fetch() ){ ?>
    
    <table width="1500" border="1" bordercolor="red"> 
      <tr> 
        <th> <?=$projet['nomProjet'];?> </th>
      </tr>

        <?php $sujetsEleve = $bdd->prepare('SELECT * FROM matchprojet NATURAL JOIN listesujets WHERE idUtilisateur = ? AND idProjet = ? AND idSujet IS NOT NULL'); 
              $sujetsEleve -> execute(array($_SESSION['idUtilisateur'],$projet['idProjet'])); 
              //Cas où l'élève a déjà choisi un sujet
              if($sujetsEleve -> rowCount() == 1){
                $sujetsEleve = $sujetsEleve -> fetch() ;  ?>

                  <?php 
                  //DEBUT initialisation de l'effectif actuel du sujet choisi
                  $nbActuel = $bdd->prepare('SELECT * FROM matchprojet WHERE idProjet = ? && idSujet = ?'); 
                  $nbActuel -> execute(array($projet['idProjet'],$sujetsEleve['idSujet'])); 
                  $effActuel = $nbActuel -> rowCount() ;
                  //FIN initialisation de l'effectif actuel du sujet choisi
                  ?>


                <table width="1500" border="1" bordercolor = "blue">
                  <tr>
                    <th width="8%"> Sujet Choisi </th>
                    <th width="3%"> Min </th>
                    <th width="4%"> Actuel </th>
                    <th width="3%"> Max </th>
                    <th width="9%"> Date Soutenance </th>
                    <th width="11%"> Lieu Soutenance </th>
                    <th width="7%"> Date Rendu </th>
                    <th width="8%"> Descriptif </th>
                    <th width="23%"> Lien Rendu </th>
                    <th width="9%"> Date Lien </th>
                    <th width="15%"> Editer Lien </th>
                </tr>

                <tr>
                  <td> <?php if(isset($sujetsEleve['nomSujet'])){ echo $sujetsEleve['nomSujet'] ; } ?> </td>
                  <td> <?php if(isset($sujetsEleve['effMin'])){ echo $sujetsEleve['effMin'] ; } ?> </td>
                  <td> <?php echo $effActuel ?> </td>
                  <td> <?php if(isset($sujetsEleve['effMax'])){ echo $sujetsEleve['effMax'] ; } ; ?> </td>
                  <td> <?php if(isset($sujetsEleve['dateSoutenance'])){ echo $sujetsEleve['dateSoutenance'] ; } ?> </td>
                  <td> <?php if(isset($sujetsEleve['lieuSoutenance'])){ echo $sujetsEleve['lieuSoutenance'] ; } ?> </td>
                  <td> <?php if(isset($sujetsEleve['dateRendu'])){ echo $sujetsEleve['dateRendu'] ; } ?> </td>
                  <td> <?php if(isset($sujetsEleve['descriptif'])){ ?> <a href=<?php echo "http://localhost/".$sujetsEleve['descriptif']?> > Consulter </a> <?php } ?> </td>
                  <td> <?php if(isset($sujetsEleve['lienRendu'])){ echo $sujetsEleve['lienRendu'] ; } ?> </td>
                  <td> <?php if(isset($sujetsEleve['dateLien'])){ echo $sujetsEleve['dateLien'] ; } ?> </td>
                  <td> <form method = "POST" > 
                       <input type = "text" name = "nouveauLien" placeholder = "Nouveau Lien"> 
                       <input type = "checkbox" name="validerNouveauLien" value= "<?php echo $projet['idProjet']?>|<?php echo $sujetsEleve['idSujet']?>" > </td>
                </tr>
              </table>
              

              <?//DEBUT Tableau servant à l'affichage du bouton de validation pour le nouveau lien ?>
              <table width = "1500"> <tr> <td width = "85%"> </td>  <td width = "15%"> <input type = "submit" value = "Valider Lien">  </td> </tr> </table> </form>
              <?//FIN Tableau servant à l'affichage du bouton de validation pour le nouveau lien ?>
            
              <?php //DEBUT Affichage des elèves ?>                  
                  <?php if(isset($_POST['afficherElevesSujets'])){
                      if($_POST['afficherElevesSujets'] == 'oui'){  ?> 
                        <table width = "1500" border="1" bordercolor="green" >
                        <br>
                          <tr>
                            <th width="40%"> Nom </th>
                            <th width="40%"> Prénom </th>
                            <th width="10%"> Groupe </th>
                            <th width= "30"> Sujet Choisi </th>
                          </tr> 
                          <?php 
                        $eleves = $bdd -> query('SELECT * FROM matchprojet NATURAL JOIN listeutilisateurs WHERE idProjet ="'.$projet['idProjet'].'" AND idSujet ="'.$sujetsEleve['idSujet'].'"');
                        while($e=$eleves->fetch()){ ?>
                          <tr>
                            <td> <?php if(isset($e['nom'])){ echo $e['nom'] ; } ?> </td>
                            <td> <?php if(isset($e['prenom'])){ echo $e['prenom'] ; } ?> </td>
                            <td> <?php if(isset($e['nbGroupe'])){ echo 'Groupe : '.$e['nbGroupe'] ; } ?> </td>
                            <td> <?php if(isset($e['idSujet'])){ 
                              $sujetName = $bdd -> prepare('SELECT * FROM listesujets WHERE idSujet = ? AND idProjet = ?');
                              $sujetName -> execute(array($e['idSujet'],$projet['idProjet']));
                              $sujetName = $sujetName -> fetch();
                              echo $sujetName['nomSujet'] ; } ?> </td>
                          </tr>
                  <?php } ?>
                    </table>
    <?php } } ?> </br> </br>
            <?php //FIN Affichage des elèves ?>

   
            <?php  //Cas où l'élève n'a pas encore choisi de sujet
              }else{
                $sujetsEleve = $bdd->prepare('SELECT * FROM listesujets WHERE idProjet = ?'); 
                $sujetsEleve -> execute(array($projet['idProjet'])); ?>
                
                <table width="1500" border="1" bordercolor = "blue">
                  <tr>
                      <th width="26%"> Nom Sujet </th>
                      <th width="3%"> Min </th>
                      <th width="5%"> Actuel </th>
                      <th width="3%"> Max </th>
                      <th width="9%"> Date Soutenance </th>
                      <th width="23%"> Lieu Soutenance </th>
                      <th width="8%"> Date Rendu </th>
                      <th width="8%"> Descriptif </th>
                      <th width="15%"> Statut </th>
                  </tr>
                <?php while($sujet = $sujetsEleve-> fetch() ){ ?>
                  <tr>
                  

                  <?php 
                  //DEBUT initialisation de l'effectif actuel du sujet ainsi que de son statut
                  $nbActuel = $bdd->prepare('SELECT * FROM matchprojet WHERE idProjet = ? && idSujet = ?'); 
                  $nbActuel -> execute(array($projet['idProjet'],$sujet['idSujet'])); 
                  $effActuel = $nbActuel -> rowCount() ;
                  if(isset($sujet['effMin']) && isset($sujet['effMax'])){
                    if($sujet['effMin'] > $sujet['effMax'] || $sujet['effMax'] < 1){
                      $statut = 'Indisponible' ;
                    }else if($effActuel >= $sujet['effMax']){
                      $statut = 'Complet';
                    }else{
                      $statut = 'Disponible';
                    }
                  }else{
                    $statut = 'Indisponible';
                  }
                  //FIN initialisation de l'effectif actuel du sujet ainsi que de son statut 
                  ?>


                    <td> <?php if(isset($sujet['nomSujet'])){ echo $sujet['nomSujet'] ; } ?> </td>
                    <td> <?php if(isset($sujet['effMin'])){ echo $sujet['effMin'] ; } ?> </td>
                    <td> <?php echo $effActuel ; ?></td>
                    <td> <?php if(isset($sujet['effMax'])){ echo $sujet['effMax'] ; } ; ?> </td>
                    <td> <?php if(isset($sujet['dateSoutenance'])){ echo $sujet['dateSoutenance'] ; } ?> </td>
                    <td> <?php if(isset($sujet['lieuSoutenance'])){ echo $sujet['lieuSoutenance'] ; } ?> </td>
                    <td> <?php if(isset($sujet['dateRendu'])){ echo $sujet['dateRendu'] ; } ?> </td>
                    <td> <?php if(isset($sujet['descriptif'])){ ?> <a href=<?php echo "http://localhost/".$sujet['descriptif']?> > Consulter </a> <?php } ?> </td>
                    <td> <?php if($statut != 'Disponible'){ echo $statut ;
                               }else{ ?>
                                 <form method = 'POST'>
                                  <input type="radio" name="choixSujet" value= "<?php echo $projet['idProjet']?>|<?php echo $sujet['idSujet']?>" /> 
                                  <?php echo $statut ; ?>
                                <?php } ?>  </td>
                  </tr>
            <?php
                } ?>
              </table>
                <?php // DEBUT Tableau servant à l'affichage du bouton de choix de sujet ?>
                <table width = "1500" border = 0>
                     <tr>
                      <td width="85%"> </td>
                      <td width="18%"> <input type="submit" value="Choisir le Sujet sélectionné"/> </td>
                    </tr>
                </table>
                </form>
                <?php // FIN Tableau servant à l'affichage du bouton de choix de sujet ?>
             
              
              <?php //DEBUT Affichage des elèves ?>                  
                  <?php if(isset($_POST['afficherElevesSujets'])){
                      if($_POST['afficherElevesSujets'] == 'oui'){  ?> 
                        <table width = "1500" border="1" bordercolor="green" >
                        <br>
                          <tr>
                            <th width="40%"> Nom </th>
                            <th width="40%"> Prénom </th>
                            <th width="10%"> Groupe </th>
                            <th width= "30"> Sujet Choisi </th>
                          </tr> 
                          <?php 
                        $eleves = $bdd -> query('SELECT * FROM matchprojet NATURAL JOIN listeutilisateurs WHERE idProjet ="'.$projet['idProjet'].'"');
                        while($e=$eleves->fetch()){ ?>
                          <tr>
                            <td> <?php if(isset($e['nom'])){ echo $e['nom'] ; } ?> </td>
                            <td> <?php if(isset($e['prenom'])){ echo $e['prenom'] ; } ?> </td>
                            <td> <?php if(isset($e['nbGroupe'])){ echo 'Groupe : '.$e['nbGroupe'] ; } ?> </td>
                            <td> <?php if(isset($e['idSujet'])){ 
                              $sujetName = $bdd -> prepare('SELECT * FROM listesujets WHERE idSujet = ? AND idProjet = ?');
                              $sujetName -> execute(array($e['idSujet'],$projet['idProjet']));
                              $sujetName = $sujetName -> fetch();
                              echo $sujetName['nomSujet'] ; } ?> </td>
                          </tr>
                  <?php } ?>
                    </table>
    <?php } } ?> </br> </br>
            <?php //FIN Affichage des elèves ?>





            <?php
            } ?>
     </table>  
  <?php } ?>

  <a href='deconnexion.php'> Déconnexion </a>
	
  </body>
</html>


