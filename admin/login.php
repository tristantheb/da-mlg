<?php
require_once "includes/config.php";
if(empty($error)) {
	$error = '';
}
if(isLogged() && isIdentified()) {
	header('Location: index.php');
} elseif(!isLogged() && isIdentified()) {
	header('Location: lockscreen.php');
} else {
	if(isset($_POST['lastname']) && isset($_POST['surname']) && isset($_POST['password'])) {
		$query = $db->prepare('SELECT * FROM advisors WHERE lastname = :lastname AND surname = :surname');
		$query->execute(array(
			'lastname' => $_POST['lastname'],
			'surname' => $_POST['surname']
		));
		$data = $query->fetch();
		if($data['lastname'] == $_POST['lastname'] && $data['surname'] == $_POST['surname']) {
			if($data['password'] == md5($_POST['password'])) {
				if ($data['advisor_id'] != '10') {
					if (isset($_POST['remember_me'])) {
						$_SESSION['logged'] = true;
						setcookie("advisor", $data['advisor_id'], strtotime("first day of next month 0:00"), null, null, true, true);
						if($data['adm_lvl'] == '1') {
							setcookie("is_admin", $data['advisor_id'], strtotime("first day of next month 0:00"), null, null, true, true);
						}
					} else {
						$_SESSION['logged'] = true;
						setcookie("advisor", $data['advisor_id'], 0, null, null, true, true);
						if($data['adm_lvl'] == '1') {
							setcookie("is_admin", $data['advisor_id'], 0, null, null, true, true);
						}
					}
					$query->closeCursor();
					$error = '<div class="alert alert-success" role="alert">Connexion en cours...</div>';
					header('Refresh: 1, index.php');
				} else {
					$error = '<div class="alert alert-danger" role="alert">Sessions des administrateurs et conseillers uniquement.</div>';
				}
			} else {
				$error = '<div class="alert alert-warning" role="alert">Mot de passe incorrect.</div>';
				$query->closeCursor();
			}
		} else {
			$error = '<div class="alert alert-warning" role="alert">Nom ou Prénom incorrect.</div>';
			$query->closeCursor();
		}
		$query->closeCursor();
	} elseif(isset($_POST['submit'])) {
		$error = '<div class="alert alert-warning" role="alert">Le formulaire n\'est pas entierement complété.</div>';
	}
}
?>
<!DOCTYPE html>
<html>
<?php include_once "./includes/tmpl_head.php"; ?>
<body class="login-page">
	<!-- Contenu principal -->
	<div class="login-box">
		<div class="alert alert-danger"><i class="fad fa-exclamation-triangle"></i> Une erreur de base de donnée a corrompue les mots de passe, pour cette raison, ils ont été réinitialisé. Utilisez <mark>Soleil*33</mark> à votre première connexion.</div>
		<div class="login-logo">
			<a href="./"><b>Admin</b>DA</a>
		</div>
		<div class="card">
			<div class="card-body login-card-body">
				<p class="login-box-msg">Se connecter à l'Administration</p>
				<form action="#" method="POST">
					<?php echo $error; ?>
					<div class="input-group mb-2">
						<input type="text" class="form-control" name="surname" id="surname" placeholder="Votre prénom">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fa fa-user"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-2">
						<input type="text" class="form-control" name="lastname" id="lastname" placeholder="Votre nom" onkeyup="this.value = this.value.toUpperCase();">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fa fa-user"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-2">
						<input type="password" class="form-control" name="password" id="password" placeholder="Mot de passe">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fa fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="custom-control custom-checkbox mb-2">
						<input class="custom-control-input" type="checkbox" name="remember_me" id="remember_me">
						<label for="remember_me" class="custom-control-label">Se souvenir de moi</label>
					</div>
					<button type="submit" id="submit" class="btn btn-primary">Valider</button>
				</form>
			</div>
		</div>
	</div>
<!-- Scripts -->
<script async src="./jscripts/lazysizes.min.js"></script>
<script defer src="./jscripts/jquery-3.4.1.min.js"></script>
<script defer src="./jscripts/scripts.js"></script>
</body>
</html>