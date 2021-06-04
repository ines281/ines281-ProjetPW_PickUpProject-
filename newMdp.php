<?php
require_once 'bdd.php';
// Init session
session_start();
// Validate login
if (!isset($_SESSION['user']) || empty($_SESSION['user']) || !isset($_SESSION['email']) || empty($_SESSION['email']))
{
    header('location: connexion.php');
    exit;
}
// Init vars
$id = $pass_1 = $pass_2 = '';
$msg = $pass_1_erreur = $pass_2_erreur = '';
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
if (empty($identifiant_erreur) && empty($pass_1_erreur) && empty($pass_2_erreur))
{
	$id= $_SESSION['user'];
    // Hash pass_1
    $pass_1 = password_hash($pass_1, PASSWORD_DEFAULT, array(
        "cost" => 10
    ));

    // Prepare insert query

     $sql = 'UPDATE listeutilisateurs SET password = :pass_1 WHERE idUtilisateur = :id';
    if ($stmt = $pdo->prepare($sql))
    {
        // Bind params
        $stmt->bindParam(':pass_1', $pass_1, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        // Attempt to execute
        if ($stmt->execute())
        {
            // Redirect to login
            $msg = "Votre mot de passe a bien été modifié";
            header('location: connexion.php?msg=Votre mot de passe a bien été modifié');
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

?>

 <!DOCTYPE html>
<html>
	<head>
		<title>Modification du mot de passe</title>
        <link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		
		<div class="container">
		<div class="login-register-text" style="text-align: center;"><?php if(isset($_GET['msg'])){echo $_GET['msg'];} ?></div>
		<form  action="" method="post" class="login-email">

		    <p class="login-text" style="font-size: 2rem; font-weight: 800;">Modification du mot de passe</p>
		   <p class="login-register-text"> Veuillez entrer votre nouveau mot de passe. </p>
		   <br/>
		    <div class="input-group">
					<label>Mot de passe:</label>
					<input type="password" name="pass_1" minlength="4" placeholder="*******" required>
					<span ><?php echo $pass_1_erreur; ?></span>
				</div>

				<div class="input-group">
					<label>Confirmez le mot de passe:</label>
					<input type="password" name="pass_2" minlength="4" placeholder="*******" required>
					<span ><?php echo $pass_2_erreur; ?></span>
				</div>
				<br/>
				<div class="input-group">
					<button type="submit" class="btn" name="inscription">Valider</button>
				</div>

		</form>
        </div>
	</body>
</html>