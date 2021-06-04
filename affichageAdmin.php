<?php
  // Init session
  session_start();
    include 'serveur.php';
  // Validate login
 if(!isset($_SESSION['utilisateur']) || empty($_SESSION['utilisateur']) || !isset($_SESSION['id']) || empty($_SESSION['id']) ){
    header('location: connexion.php');
    exit;
  }
require 'db-config.php';

try
{
    $options =
    [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASS, $options);
    $requeteNomsProjets = 'SELECT * FROM listeprojets ORDER BY idProjet DESC';
    $nomsProjets = $bdd->query($requeteNomsProjets);
}
catch(PDOException $pe)
{
    echo 'ERREUR : '.$pe->getMessage();
};

//DEBUT Création de projets
if(isset($_POST['NomProjet'])){
    if(!empty($_POST['NomProjet'])){
        $nomProjet = htmlspecialchars($_POST['NomProjet']);
        $genererProjet = $bdd -> prepare('INSERT INTO listeprojets(nomProjet) VALUES (?)');
        $genererProjet -> execute(array($nomProjet));
        $messageCreationProjet = 'Le projet a bien été créé.';
    }else{
        $messageCreationProjet = 'Veuillez saisir le nom du projet.';
    }
}
//FIN Création de projets

//DEBUT Gestion des élèves
if(isset($_POST['nom']) && isset($_POST['prénom']) && isset($_POST['classe'])){
    if(!empty($_POST['nom']) && !empty($_POST['prénom']) && !empty($_POST['classe'])){
        if(isset($_POST['gerer']) AND !empty($_POST["gerer"])){
            $id = $_POST['prénom'][0].$_POST['nom'];
            $mdp = password_hash($id, PASSWORD_DEFAULT, array("cost" => 10));
            if($_POST['gerer'] == 'ajouter'){
                $gererEtudiant = $bdd -> prepare('INSERT INTO listeutilisateurs(nom,prenom,identifiant,password,nbGroupe,admin) VALUES (?,?,?,?,?,0)');
                $gererEtudiant -> execute(array($_POST['nom'],$_POST['prénom'],$id,$mdp,$_POST['classe']));
                $messageGererEtudiant = 'Le compte de cet étudiant a été créé.';
            }else{
                $idEtudiant = $bdd -> prepare('SELECT * FROM listeutilisateurs WHERE nom=? AND prenom=? AND nbGroupe=?');
                $idEtudiant -> execute(array($_POST['nom'],$_POST['prénom'],$_POST['classe']));
                if($idEtudiant -> rowCount() >= 1){
                    $idEtudiant = $idEtudiant -> fetch();
                    if($_POST['gerer'] == 'supprimer'){
                        $removeEtudiant = $bdd -> prepare('DELETE FROM listeutilisateurs WHERE idUtilisateur=?');
                        $removeEtudiant -> execute(array($idEtudiant['idUtilisateur']));
                        $removeEtudiant2 = $bdd -> prepare('DELETE FROM matchprojet WHERE idUtilisateur=?');
                        $removeEtudiant2 -> execute(array($idEtudiant['idUtilisateur']));
                        $messageGererEtudiant = 'Cet élève a bien été supprimé.';
                    }else if($_POST['gerer'] == 'modifier'){
                        $editEtudiant = $bdd -> prepare('UPDATE listeutilisateurs SET nbGroupe=? WHERE idUtilisateur=?');
                        $editEtudiant -> execute(array($_POST['classeModif'],$idEtudiant['idUtilisateur']));
                        $messageGererEtudiant = 'La classe de cet élève a bien été modifiée.';
                    }
                }else{
                    $messageGererEtudiant = 'Cet étudiant n\'existe pas.';
                } 
            }
        }else{
            $messageGererEtudiant = 'Veuillez indiquer l\'action à effectuer.';
        }  
    }
}
//FIN Gestion des élèves


?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<head>
    <title> Admin Index </title>
    <link rel="stylesheet" type="text/css" href="page.css">
</head>
<body> 
    <a class = "dec" href='deconnexion.php'> Déconnexion</a>
    <br/>
    <h1> Créer un Projet: <h1>
        <br/>
    <form method="POST">
        <input type="text" name="NomProjet" placeholder="Nom du Projet"/>
        <input type="submit" value="Créer le Projet"/>
    </form>
    <?php if(isset($messageCreationProjet)) echo $messageCreationProjet ; ?>

    <br/>
    <h2> Liste des Projets: <h2> 
        <br/>
    <ul>
        <?php while($projet = $nomsProjets -> fetch()) { ?>
            <li> <a href="projetAdmin.php?idProjet=<?= $projet['idProjet'] ?> "> <?= $projet['idProjet'] ?> <?= $projet['nomProjet'] ?> </a>
             | <a href="modifProjetAdmin.php?edit=<?= $projet['idProjet'] ?>" > Modifier </a> | <a href="modifProjetAdmin.php?edit=<?= $projet['idProjet'] ?>&suppressio" > Supprimer </a>
        <?php } ?>
    <ul>
    <br/>
    <h3> Gérer Étudiants : <h3>
    <br/>
    <form method="POST">
    <p> <input type="text" name="prénom" placeholder = "Prénom" />
        <input type="text" name="nom" placeholder = "Nom" />
        <select name="classe" size="1"> 
                 <option value = "1">Groupe 1</option>
                 <option value = "2">Groupe 2</option>
                 <option value = "3">Groupe 3</option>
                 <option value = "4">Groupe 4</option>
                 <option value = "5">Groupe 5</option> </select> </p> 
    <br>
    <p> <input type="radio" name="gerer" value="ajouter" /> Ajouter Étudiant
        <input type="radio" name="gerer" value="supprimer" /> Supprimer Étudiant
        <input type ="radio" name="gerer" value="modifier" /> 
        Modifier Classe <select name="classeModif" size="1"> 
                        <option value = "1">Groupe 1</option>
                        <option value = "2">Groupe 2</option>
                        <option value = "3">Groupe 3</option>
                        <option value = "4">Groupe 4</option>
                        <option value = "5">Groupe 5</option> </select> 

    <button type="submit" name="validation"> <em>    Valider</em> </button>
    <button type="reset" name="annuler"> <em>    Réinitialiser</em> </button>
    </p> 
    </form>
    <br>
    <br>
    <p> Note : si vous créez un étudiant, son id/mdp pour sa première connexion sera la première lettre de son prénom suivie de son nom.
               Par exemple, 'Virginie Sans' aura pour identifiants 'VSans'. <p>
    <?php if(isset($messageGererEtudiant)){ echo $messageGererEtudiant ; } ?>

<br>
    <a href='deconnexion.php'> Déconnexion</a>

</body>
 </html>

