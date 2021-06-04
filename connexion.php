<?php
include 'serveur.php'; ?>
<!DOCTYPE html>
<html>
	<head>
		<title>Connexion</title>
        <link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		
		<div class="container">
		<div class="login-register-text" style="text-align: center;"><?php if(isset($_GET['msg'])){echo $_GET['msg'];} ?></div>
		<form  action="" method="post" class="login-email">

		    <p class="login-text" style="font-size: 2rem; font-weight: 800;">Connectez-vous</p>

		    <div class="input-group">
			<input type="text" name="ps" placeholder="Votre identifiant" required>
			<span ><?php echo $ps_erreur; ?></span>
			</div>

			<div class="input-group">
			<input type="password" name="mdp" placeholder="******" required>
			<span ><?php echo $mdp_erreur; ?></span>
			</div>

			<div class="input-group">
			<button type="submit" class="btn" value="connexion">Connexion</button>
			</div>

			<p class="login-register-text">
				Pas encore inscrit?
				<a href="inscription.php">Inscrivez-vous maintenant</a>
			</p>

			<p class="login-register-text">

				<a href = "resetmdp.php" >Vous avez oublié votre mot de passe ?</a>
			</p>

		</form>
        </div>
	</body>
</html>