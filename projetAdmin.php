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



//DEBUT Traitement réservé au projet choisi
    //on vérifie que le projet existe et on récupère son id
if(isset($_GET['idProjet']) AND !empty($_GET['idProjet'])) {
    $get_idprojet = htmlspecialchars($_GET['idProjet']);
    $projet = $bdd -> prepare('SELECT * FROM listeprojets WHERE idprojet = ?');
    $projet -> execute(array($get_idprojet));
    if($projet -> rowCount() == 1){
        //on récupère le nom du projet et la liste de ses sujets
        $projet = $projet -> fetch();
        $nomProjet = $projet['nomProjet'];
        $sujets = $bdd -> query('SELECT * FROM listesujets WHERE idProjet ="'.$get_idprojet.'"');
    }else{
        die('Ce projet n\'existe pas.');
    }
}else{
    die('Erreur.');
}
//FIN Traitement réservé au projet choisi



//DEBUT Traitement réservé à l'ajout de sujets au projet.
if(isset($_POST['NomSujet'])){
    if(!empty($_POST['NomSujet'])){
        $nomSujet = htmlspecialchars($_POST['NomSujet']);
        $genererSujet = $bdd -> prepare('INSERT INTO listesujets(idProjet,nomsujet) VALUES (?,?)');
        $genererSujet -> execute(array($get_idprojet,$nomSujet));
        $messageCreationSujet = 'Le sujet a bien été créé.';
    }else{
        $messageCreationSujet = 'Veuillez saisir le nom du sujet.';
    }
}
//FIN Traitement réservé à l'ajout de sujets au projet.



//DEBUT Traitement réservé à l'ajout d'élèves au projet.
if(isset($_POST['NomEleve']) && isset($_POST['PrenomEleve']) && isset($_POST['ClasseEleve'])){
    if(!empty($_POST['NomEleve']) && !empty($_POST['PrenomEleve']) && !empty($_POST['ClasseEleve'])){

       //On Vérifie que l'élève existe bel et bien dans la table listeUtilisateurs
       $requeteEleve = $bdd -> prepare('SELECT COUNT(*) FROM listeutilisateurs WHERE nom=:nom AND prenom=:prenom AND nbGroupe=:nbGroupe');
       $requeteEleve -> execute(array(':nom' => $_POST['NomEleve'], ':prenom' => $_POST['PrenomEleve'], ':nbGroupe' => $_POST['ClasseEleve']));
       if($requeteEleve->fetchColumn() == 1){
        
        //On récupère l'idUtilisateur de l'élève
        $idEleveAffectation = $bdd -> query('SELECT idUtilisateur FROM listeutilisateurs WHERE nom="'.$_POST['NomEleve'].'" AND prenom="'.$_POST['PrenomEleve'].'" AND nbGroupe="'.$_POST['ClasseEleve'].'"');
        while($elaf=$idEleveAffectation->fetch()){ 
            if(isset($elaf['idUtilisateur'])){ $idEleveAffect = $elaf['idUtilisateur'] ; } 
        }

        //on vérifie que l'élève n'est pas déjà affecté à ce projet
        $verifProjetEleve = $bdd -> prepare('SELECT COUNT(*) FROM matchprojet WHERE idProjet=:idProjet AND idUtilisateur=:idUtilisateur');
        $verifProjetEleve -> execute(array(':idProjet' => $_GET['idProjet'], ':idUtilisateur' => $idEleveAffect));
        if($verifProjetEleve->fetchColumn() == 0){
            
            //on ajoute l'élève au projet, c'est-à-dire dans la table matchprojet
            $ajoutEleveProjet = $bdd -> prepare('INSERT INTO matchprojet VALUES(?,?,null)');
            $ajoutEleveProjet -> execute(array($_GET['idProjet'],$idEleveAffect));
            $messageAffectationEleve = 'Cet élève a bien été ajouté au projet.';

        }else{
            $messageAffectationEleve = 'Cet élève est déjà affecté à ce projet.';
        }
       }else{
        $messageAffectationEleve = 'Cet élève n\'existe pas.';
       } 
    }else{ 
        $messageAffectationEleve = 'Veuillez renseigner tous les champs.';
    }
}
//FIN Traitement réservé à l'ajout d'élèves au projet.

?>





<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<head>   
    <title> <?= $nomProjet ?> </title>
        <link rel="stylesheet" type="text/css" href="page.css">
</head>
<body> 

<a href="affichageAdmin.php" > Retour </a>
<a href='deconnexion.php'> Déconnexion</a>

    <br/>
    <h1> <?= $nomProjet ?> <h1>
    <br/>

    <h2> Créer un Sujet <h2>
    
    <form method="POST">
        <input type="text" name="NomSujet" placeholder="Nom du Sujet"/>
        <input type="submit" value="Créer le Sujet"/>
    </form>
    <br/>
    <br/>
    <h3> Ajouter des élèves à ce projet <h3>
    <form method="POST">
        <p> <input type="text" name="NomEleve" placeholder="Nom Élève"/>
        <input type="text" name="PrenomEleve" placeholder="Prénom Élève"/> 
         Groupe : <select name="ClasseEleve" size="1"> 
                        <option value = "1">G1</option>
                        <option value = "2">G2</option>
                        <option value = "3">G3</option>
                        <option value = "4">G4</option>
                        <option value = "5">G5</option> </select> 
        <input type="submit" value="Ajouter"/> <p>
    </form>
    <?php if(isset($messageAffectationEleve)){ echo $messageAffectationEleve ; } ?>
    <br/> <br>

    <form method="POST">
        <p> Afficher l'affectation des élèves aux sujets ?
        <input type="checkbox" name="afficherElevesSujets" value="oui" /> 
        <input type="submit" value="Valider"/>
    </form>

    <br>

    <?php if(isset($messageCreationSujet)) { echo $messageCreationSujet ; } ?>

    
    <?php while ($s = $sujets->fetch()){ ?>
    
        <table width="1500" border="1" bordercolor="red"> 
    <tr> 
        <th width="7"> </th>
        <th width="7"> </th>
        <th width="20%"> Nom du Sujet </th>
        <th width="5%"> Min </th>
        <th width="5%"> Max </th>
        <th width="10%"> Date Soutenance </th>
        <th width="15%"> Lieu Soutenance </th>
        <th width="10%"> Date Rendu </th>
        <th width="20%"> Lien Rendu </th>
        <th width="15%"> Date Lien </th>
        <th width="10%"> Descriptif </th>
    </tr>
    
    <tr>
        <td> <a href="modifSujetAdmin.php?idProjet=<?=$_GET['idProjet'];?>&editSujet=<?=$s['idSujet'];?>&suppression=1"> Supprimer </a> </td> 
        <td> <a href="modifSujetAdmin.php?idProjet=<?=$_GET['idProjet'];?>&editSujet=<?=$s['idSujet'];?>" > Modifier </a> </td> 
        <td> <?php if(isset($s['nomSujet'])){ echo $s['nomSujet'] ; } ?> </td>
        <td> <?php if(isset($s['effMin'])){ echo $s['effMin'] ; } ?> </td>
        <td> <?php if(isset($s['effMax'])){ echo $s['effMax'] ; } ; ?> </td>
        <td> <?php if(isset($s['dateSoutenance'])){ echo $s['dateSoutenance'] ; } ?> </td>
        <td> <?php if(isset($s['lieuSoutenance'])){ echo $s['lieuSoutenance'] ; } ?> </td>
        <td> <?php if(isset($s['dateRendu'])){ echo $s['dateRendu'] ; } ?> </td>
        <td> <?php if(isset($s['lienRendu'])){ echo $s['lienRendu'] ; } ?> </td>
        <td> <?php if(isset($s['dateLien'])){ echo $s['dateLien'] ; } ?> </td>
        <td> <?php if(isset($s['descriptif'])){ ?> <a href=<?php echo "http://localhost/".$s['descriptif']?> > Consulter </a> <?php } ?> </td>
    </tr>

    <?php if(isset($_POST['afficherElevesSujets'])){
        if($_POST['afficherElevesSujets'] == 'oui'){  ?> 
            <table width = "1500" border="1" bordercolor="green" >
            <br>
            <tr>
                <th width="40%"> Nom </th>
                <th width="40%"> Prénom </th>
                <th width="20%"> Groupe </th>
            </tr>
                <?php 
                $eleves = $bdd -> query('SELECT * FROM matchprojet NATURAL JOIN listeutilisateurs WHERE idSujet ="'.$s['idSujet'].'" AND idProjet ="'.$_GET['idProjet'].'"');
                while($e=$eleves->fetch()){ ?>
                   <tr>
                        <td> <?php if(isset($e['nom'])){ echo $e['nom'] ; } ?> </td>
                        <td> <?php if(isset($e['prenom'])){ echo $e['prenom'] ; } ?> </td>
                        <td> <?php if(isset($e['nbGroupe'])){ echo 'Groupe : '.$e['nbGroupe'] ; } ?> </td>
                    </tr>
                <?php } ?>
            </table>

    <?php } } ?> </br> </br>


    <?php } ?>

    <?php if(isset($_POST['afficherElevesSujets'])){
        if($_POST['afficherElevesSujets'] == 'oui'){  ?> 
        <table width = "1500" border="1" bordercolor="blue" >
                <br>
                <tr>
                    <th width="40%"> Nom </th>
                    <th width="40%"> Prénom </th>
                    <th width="20%"> Groupe </th>
                </tr>
                    <?php 
                    $eleves = $bdd -> query('SELECT * FROM matchprojet NATURAL JOIN listeutilisateurs WHERE idSujet IS NULL AND idProjet ="'.$_GET['idProjet'].'"');
                    while($e=$eleves->fetch()){ ?>
                    <tr>
                            <td> <?php if(isset($e['nom'])){ echo $e['nom'] ; } ?> </td>
                            <td> <?php if(isset($e['prenom'])){ echo $e['prenom'] ; } ?> </td>
                            <td> <?php if(isset($e['nbGroupe'])){ echo 'Groupe : '.$e['nbGroupe'] ; } ?> </td>
                        </tr>
                    <?php } ?>
                </table>
<?php } } ?> </br> </br>

 


</body>


 </html>

