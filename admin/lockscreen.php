<?php
require_once "includes/config.php";
if(empty($error)) {
	$error = '';
}
if(isLogged()) {
	header('Location: index.php');
} elseif (!isLogged() && !isIdentified()) {
    header('Location: login.php');
} else {
    $query = $db->prepare('SELECT * FROM advisors WHERE advisor_id = :advisor');
    $query->execute(array(
        'advisor' => $_COOKIE['advisor']
    ));
    $data = $query->fetch();
    $full_name = $data['full_name'];
    $query->closeCursor();
	if(isset($_POST['password'])) {
        if($data['password'] == md5($_POST['password'])) {
            $_SESSION['logged'] = true;
            $query->closeCursor();
            $error = '<div class="alert alert-success" role="alert">Connexion en cours...</div>';
            header('Refresh: 1, index.php');
        } else {
            $error = '<div class="alert alert-warning" role="alert">Mot de passe incorrect.</div>';
        }
	} elseif(isset($_POST['submit'])) {
		$error = '<div class="alert alert-warning" role="alert">Le formulaire n\'est pas entierement complété.</div>';
	}
	$query->closeCursor();
}
?>
<!DOCTYPE html>
<html>
<?php include_once "./includes/tmpl_head.php"; ?>
<body class="lockscreen">
	<!-- Contenu principal -->
	<div class="lockscreen-wrapper">
		<div class="lockscreen-logo">
			<a href="./"><b>Admin</b>DA</a>
		</div>
        <?php echo $error; ?>
		<!-- User name -->
		<div class="lockscreen-name"><?php echo $full_name; ?></div>
		<!-- START LOCK SCREEN ITEM -->
		<div class="lockscreen-item">
			<!-- lockscreen image -->
			<div class="lockscreen-image">
			    <img src="./c_images/user.png" alt="User Image">
			</div>
			<!-- /.lockscreen-image -->
			<!-- lockscreen credentials (contains the form) -->
			<form class="lockscreen-credentials" action="" method="POST">
                <div class="input-group">
                    <input type="password" name="password" class="form-control" placeholder="Mot de passe">
                    <div class="input-group-append">
                        <button type="submit" name="submit" class="btn"><i class="fas fa-arrow-right text-muted"></i></button>
                    </div>
                </div>
			</form>
			<!-- /.lockscreen credentials -->
		</div>
  		<!-- /.lockscreen-item -->
		<div class="help-block text-center">
			Entrez votre mot de passe pour vous reconnecter.
		</div>
		<div class="lockscreen-footer text-center">
            Administration Garantie Jeunes <strong>Copyright &copy; 2019 - <?php echo date('Y'); ?></strong>, tous droits réservés.
		</div>
	</div>
<!-- Scripts -->
<script async src="./jscripts/lazysizes.min.js"></script>
<script defer src="./jscripts/jquery-3.4.1.min.js"></script>
<script defer src="./jscripts/scripts.js"></script>
</body>
</html>