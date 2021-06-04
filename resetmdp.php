<?php
require_once 'bdd.php';
$email  = '';
$email_erreur = '';
// Process form when post submit
if (!empty($_POST))
{
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    //pour l'authentification ---------------------------------------------------------------------------------------------------------------------------
    if (isset($_POST['email']) )
    {
        $email = trim($_POST['email']);
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $email_erreur  = "Veuillez saisir une adresse email valide";
        else 
        {
        	 // Prepare a select statement
            $sql = 'SELECT * FROM listeutilisateurs WHERE email = :email ';

            if ($stmt = $pdo->prepare($sql))
            {
                // Bind variables
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                // Attempt to execute
                if ($stmt->execute())
                {
                    // Check if email exists
                    if ($stmt->rowCount() === 1)
                    {
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($result)
                        {
                        	$_SESSION['user'] = $result['idUtilisateur'];
                        	$_SESSION['email'] = $email;
                             $messageMail = 'Réinitialisez votre mot de passe "<a href ="newMdp.php?msg=e mail envo&#233</a>"';
                             $subject ='Mot de Passe';
                             if(mail($email,$subject,$messageMail)){
                             	echo" yes";
                			 }else{
                    			echo 'erreur lors de l\'envoi du mail';
                			}
                            	
                        }//fin result
                    }
                }
                else
                {
                    die('Something went wrong');
                }
            }

            unset($stmt);
        } //else filter ----
	}
}


?>
<!DOCTYPE html>
<html>
	<head>
		<title>Mot de passe oubli&#233;</title>
        <link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		
		<div class="container">
		<form  action="#" method="post" class="login-email">

		    <p class="login-text" style="font-size: 2rem; font-weight: 800;">Mot de passe oubli&#233;</p>
		   <p class="login-register-text"> Veuillez entrer votre adresse e-mail afin que nous puissions vous aider &#224; r&#233;cup&#233;rer votre compte. </p>
		   <br/>
		    <div class="input-group">
			<input type="text" name="email" placeholder="Votre e-mail" required>
			<span ><?php echo $email_erreur; ?></span>
			</div>
			<div class="input-group">
			<button type="submit" class="btn" value="valide">Valider</button>
			</div>

		</form>
        </div>
	</body>
</html>