<?php include 'serveur.php';
?>
<!DOCTYPE html>
<html>

	<head>
		<title>Inscription</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>

	<body>

		<div class="container">
			<form method="post" action="" class="login-email">
				<p class="login-text" style="font-size: 2rem; font-weight: 500;">Créer un compte</p>

				<div class="input-group">
					<label>Nom : </label>
					<input type="text" name="nom" placeholder="Votre nom" required>
				</div>

				<div class="input-group">
					<label>Prénom : </label>
					<input type="text" name="prenom" placeholder="Votre prénom" required>
				</div>

				<div class="input-group">
					<label>Identifiant : </label>
					<input type="text" name="pseudo" placeholder="Votre identifiant" required>
					<span ><?php echo $identifiant_erreur; ?></span>
				</div>

				<div class="input-group">
                					<label>E-mail : </label>
                					<input type="text" name="email" placeholder="Votre e-mail" required>
                					<span ><?php echo $email_erreur; ?></span>
                </div>

				<div>
					<label>Classe: </label>
					<br/>
					<div>
			        <select name = "Classe" id = "Classe"  style="  width: 100%; height:35px; border-radius: 30px;" >
                    <option selected="selected" hidden selected>...</option>
                    <option value = "1" >        Groupe 1              </ option >
                    <option value = "2" >        Groupe 2              </ option >
                    <option value = "3" >        Groupe 3              </ option >
                    <option value = "4" >        Groupe 4              </ option >
                    <option value = "5" >        Groupe 5              </ option >
                    </select>
                    </div>
				</div>

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

				<p  class="login-register-text">
					  Déjà membre?
					<a href="connexion.php">Connectez-vous</a>
				</p>
			</form>

		</div>

	</body>
</html>