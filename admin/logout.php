<?php
require "includes/config.php";
setcookie('advisor', '', 1);
if(isAdmin()) {
	setcookie('is_admin', '', 1);
}
session_unset();
session_destroy();
header('Refresh: 2;login.php')
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
		<div class="lockscreen-item">
			<div class="alert alert-info">Déconnexion en cours... Merci de patienter, vous allez être redirigé.</div>
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