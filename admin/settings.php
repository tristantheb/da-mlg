<?php
require_once "includes/config.php";
if (!isLogged() && !isIdentified()) {
	header('Location: ./login.php');
} elseif (!isLogged() && isIdentified()) {
	header('Location: ./lockscreen.php');
}
// Init $error
if (empty($error)) {
	$error = '';
}
// Get advisors informations
$query = $db->prepare('SELECT * FROM advisors WHERE advisor_id = :id');
$query->execute(array(
	'id' => $_COOKIE['advisor']
));
$data = $query->fetch();
$query->closeCursor();
// Auto get notifications
if (isAdmin()) {
	$query = $db->prepare('SELECT * FROM notifications WHERE readed = "false"');
} else {
	$query = $db->prepare('SELECT * FROM notifications WHERE readed = "false" AND advisor_id = :advisor');
}
$query->execute(array(
	'advisor' => $_COOKIE['advisor']
));
$count = $query->rowCount();
if ($count > 0) {
	$notifs = '<span class="right badge badge-danger">'.$count.'</span>';
} else {
	$notifs = '<span class="right badge badge-secondary">'.$count.'</span>';
}
$query->closeCursor();
// Post password change
$error = '';
if (isset($_POST['submit'])) {
	if (isset($_POST['old_pass']) && isset($_POST['new_pass']) && !empty($_POST['old_pass']) && !empty($_POST['new_pass'])) {
		if ($data['password'] === md5($_POST['old_pass'])) {
			if ($_POST['new_pass'] === $_POST['confirm_pass']) {
				$query = $db->prepare('UPDATE advisors SET password = :pass WHERE advisor_id = :id');
				$query->execute(array(
					"pass" => md5($_POST['new_pass']),
					"id" => $_COOKIE['advisor']
				));
				$query->closeCursor();
				$error = '<div class="alert alert-success"><strong>Success :</strong> Le mot de passe a été changé par ' . $_POST['new_pass'] . '</div>';
			} else {
				$error = '<div class="alert alert-danger"><strong>Input error :</strong> Le mot de passe de confirmation ne correspond pas.</div>';
			}
		} else {
			$error = '<div class="alert alert-danger"><strong>Input error :</strong> L\'ancien mot de passe et le nouveau sont identiques.</div>';
		}
	} else {
		$error = '<div class="alert alert-danger"><strong>Input error :</strong> Le formulaire est vide.</div>';
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<?php include_once "./includes/tmpl_head.php"; ?>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
	<!-- Navigation -->
	<?php include_once "includes/tmpl_nav.php"; ?>
	<!-- Main Sidebar Container -->
	<?php include_once "includes/tmpl_sidebar.php"; ?>
	<!-- Contenu principal -->
    <div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Paramètres</h1>
						<p class="m-0 text-muted">Ici vous pouvez facillement changer votre mot de passe.</p>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Paramètres</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main content -->
		<div class="content">
			<div class="row">
				<div class="col-md-3">
					<div class="card card-primary card-outline">
						<div class="card-body box-profile text-center">
							<div class="">
								<img class="profile-user-img img-fluid img-circle" src="./c_images/user.png" alt="User profile picture">
							</div>
							<h3 class="profile-username"><?php echo $data['full_name']; ?></h3>
							<?php
							if ($data['adm_lvl'] === '1') {
								echo '<span class="badge badge-danger">Administrateur</span>';
							} elseif ($data['adm_lvl'] === '2') {
								echo '<span class="badge badge-info">Conseiller</span>';
							}
							?>
						</div>
					</div>
				</div>
				<!-- /.col -->
				<div class="col-md-9">
					<div class="card">
						<div class="card-header p-2">
							<h4 class="card-title">Changement de mot de passe</h4>
						</div>
						<div class="card-body">
							<form action="" method="POST">
								<div class="form-group">
									<label for="old_pass">Ancien mot de passe :</label>
									<div class="input-group flex-nowrap">
										<div class="input-group-prepend">
											<span class="input-group-text" id="addon-wrapping"><i class="fad fa-lock"></i></span>
										</div>
										<input type="text" id="old_pass" name="old_pass" class="form-control" placeholder="Ancien mot de passe" aria-describedby="addon-wrapping">
									</div>
								</div>
								<div class="form-group">
									<label for="new_pass">Nouveau mot de passe :</label>
									<div class="input-group flex-nowrap">
										<div class="input-group-prepend">
											<span class="input-group-text" id="addon-wrapping"><i class="fad fa-lock"></i></span>
										</div>
										<input type="text" id="new_pass" name="new_pass" class="form-control" placeholder="Nouveau mot de passe" aria-describedby="addon-wrapping">
									</div>
								</div>
								<div class="form-group">
									<label for="confirm_pass">Répéter le mot de passe :</label>
									<div class="input-group flex-nowrap">
										<div class="input-group-prepend">
											<span class="input-group-text" id="addon-wrapping"><i class="fad fa-lock"></i></span>
										</div>
										<input type="text" id="confirm_pass" name="confirm_pass" class="form-control" placeholder="Répéter le mot de passe" aria-describedby="addon-wrapping">
									</div>
								</div>
								<input type="submit" id="submit" name="submit" class="btn btn-success float-right" value="Enregistrer">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
    <!-- Footer -->
    <footer class="main-footer">
        Administration Garantie Jeunes <strong>Copyright &copy; 2019 - <?php echo date('Y'); ?></strong>, tous droits réservés.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> <?php echo VERSION ?>
        </div>
    </footer>
</div>
<!-- Scripts -->
<script src="./jscripts/jquery-3.4.1.min.js"></script>
<script src="./jscripts/sweetalert2.min.js"></script>
<script src="./jscripts/bootstrap.bundle.min.js"></script>
<script src="./jscripts/bootstrap.min.js"></script>
<script src="./jscripts/scripts.js"></script>
</body>
</html>