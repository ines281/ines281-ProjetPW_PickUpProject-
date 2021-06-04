<?php
require_once 'bdd.php';
// Init vars
$ps = $mdp = '';
$ps_erreur = $mdp_erreur = '';
//*******************************************************************************************
// Init vars
$nom = $prenom = $identifiant = $email = $classe = $pass_1 = $pass_2 = '';
$nom_erreur = $prenom_erreur = $identifiant_erreur = $email_erreur = $pass_1_erreur = $pass_2_erreur = '';
$msg = '';
//******************************************************************************************
// Process form when post submit
if (!empty($_POST))
{
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    //pour l'authentification ---------------------------------------------------------------------------------------------------------------------------
    if (isset($_POST['ps']) && isset($_POST['mdp']))
    {
        $ps = trim($_POST['ps']);
        $mdp = trim($_POST['mdp']);

        if (empty($ps_erreur) && empty($mdp_erreur))
        {
            // Prepare query
            $sql = 'SELECT * FROM listeutilisateurs WHERE identifiant = :ps';

            // Prepare statement
            if ($stmt = $pdo->prepare($sql))
            {
                // Bind params
                $stmt->bindParam(':ps', $ps, PDO::PARAM_STR);

                // Attempt execute
                if ($stmt->execute())
                {
                    // Check if email exists
                    if ($stmt->rowCount() === 1)
                    {
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                       /* if ($result)
                        {
                            if ($result['admin'] == 0)
                            {
                                if (password_verify($mdp, $result['password']))
                                {
                                    // SUCCESSFUL LOGIN
                                    session_start();
                                    $_SESSION['utilisateur'] = $ps;
                                    $_SESSION['id'] = $result['admin'];
                                     $_SESSION['idUtilisateur'] = $result['idUtilisateur'];
                                    header('location: accueil.php');

                                }
                            }
                            else if ($mdp == $result['password'])
                            {
                                // SUCCESSFUL LOGIN
                                session_start();
                                $_SESSION['utilisateur'] = $ps;
                                $_SESSION['id'] = $result['admin'];
                                header('location: affichageAdmin.php');
                            }
                            else
                            {
                                // Display wrong password message
                                $mdp_erreur = "Le mot de passe n'est pas valide";
                            }
                        }*/

                        if ($result)
                        {
                            
                                if (password_verify($mdp, $result['password']))
                                {
                                    // SUCCESSFUL LOGIN
                                    session_start();
                                    $_SESSION['utilisateur'] = $ps;
                                    $_SESSION['id'] = $result['admin'];
                                    $_SESSION['idUtilisateur'] = $result['idUtilisateur'];
                                    if ($result['admin'] == 0)  header('location: accueil.php');
                                    else header('location: affichageAdmin.php');
                                }

                            else
                            {
                                $mdp_erreur = "Le mot de passe n'est pas valide";
                            }

                            }
                        }// ==1
                    else
                    {
                        $ps_erreur = 'Aucun compte trouvé pour cet identifiant';
                    }
                }
                else
                {
                    die('Something went wrong');
                }
            }
            // Close statement
            unset($stmt);
        }
    } //fin l'authentification ------------------------------------------------------------------------------------------------
    //****************************************************************************************************************************
    //pour l'inscription ---------------------------------------------------------------------------------------------------------------------------
    if (isset($_POST['pseudo']) && isset($_POST['pass_1']) && isset($_POST['pass_2']) && isset($_POST['Classe']))
    {
        // Put post vars in regular vars
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $identifiant = trim($_POST['pseudo']);
        $email = trim($_POST['email']);
        $classe = trim($_POST['Classe']);
        $pass_1 = trim($_POST['pass_1']);
        $pass_2 = trim($_POST['pass_2']);

        // Validate email
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            // Prepare a select statement
            $sql = 'SELECT * FROM listeutilisateurs WHERE email = :email or identifiant =:identifiant';

            if ($stmt = $pdo->prepare($sql))
            {
                // Bind variables
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':identifiant', $identifiant, PDO::PARAM_STR);
                // Attempt to execute
                if ($stmt->execute())
                {
                    // Check if email exists
                    if ($stmt->rowCount() === 1)
                    {
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($result)
                        {
                            if ($result['email'] == $email) $email_erreur = "L'email est déjà prise";
                            else $identifiant_erreur = "L'identifiant est déjà pris";
                        }
                    }
                }
                else
                {
                    die('Something went wrong');
                }
            }

            unset($stmt);
        }
        else
        {
            $email_erreur = "Veuillez saisir une adresse email valide";
        }
        //validate password
        if (strlen($pass_1) < 4)
        {
            $pass_1_erreur = 'Le mot de passe doit comporter au moins 4 caractères ';
        }

        // Validate Confirm pass_1
        if ($pass_1 !== $pass_2)
        {
            $pass_2_erreur = 'Les mots de passe ne correspondent pas';
        }

        // Make sure erreurors are empty
        if (empty($email_erreur) && empty($identifiant_erreur) && empty($pass_1_erreur) && empty($pass_2_erreur))
        {
            // Hash pass_1
            $pass_1 = password_hash($pass_1, PASSWORD_DEFAULT, array(
                "cost" => 10
            ));

            // Prepare insert query
            $sql = 'INSERT INTO listeutilisateurs (`nom`, `prenom`, `identifiant`, `password`, `nbGroupe`, `email`)
                      VALUES (:nom, :prenom,:identifiant, :pass_1, :classe, :email )';

            if ($stmt = $pdo->prepare($sql))
            {
                // Bind params
                $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
                $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
                $stmt->bindParam(':identifiant', $identifiant, PDO::PARAM_STR);
                $stmt->bindParam(':pass_1', $pass_1, PDO::PARAM_STR);
                $stmt->bindParam(':classe', $classe, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                // Attempt to execute
                if ($stmt->execute())
                {
                    // Redirect to login
                    $msg = "Votre compte a bien été crée";
                    header('location: connexion.php?msg=Votre compte a bien été crée');
                }
                else
                {
                    die('Something went wrong');
                }
            }
            unset($stmt);
        }

        // Close connection
        unset($pdo);
    }
    //****************************************************************************************************************************

}

?>
