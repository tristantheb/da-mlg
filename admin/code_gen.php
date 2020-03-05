<?php
require_once "./includes/config.php";
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
// Post generate link
$link = "";
if (isset($_POST['submit'])) {
	$ok = false;
	while($ok == false) {
		$string = "";
		$chaine = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		srand((double)microtime()*1000000);
		for($i=0; $i<34; $i++) {
			$string .= $chaine[rand()%strlen($chaine)];
			if($i == 12 || $i == 20) {
				$string .= "-";
			}
		}
		$query = $db->prepare('SELECT * FROM link_keys WHERE link_key = :key');
		$query->execute(array(
			'key' => $string
		));
		$data = $query->fetch();
		$query->closeCursor();
		if ($data != 0) {
			$ok = false;
		} else {
			$query = $db->prepare('INSERT INTO link_keys(link_key, advisor_id, created_date) VALUES(:key, :advisor, NOW())');
			$query->execute(array(
				'key' => $string, 
				'advisor' => $_COOKIE['advisor']
			));
			$query->closeCursor();
			$ok = true;
			$link = "https://www.missionlocaledesgraves.fr/da-garantie-jeunes/?key=" . $string;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php include_once "./includes/tmpl_head.php"; ?>
<link rel="stylesheet" type="text/css" href="./style/sweetalert2.min.css">
<link rel="stylesheet" type="text/css" href="./style/toastr.min.css">
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
						<h1 class="m-0 text-dark">Générateur de lien</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Générateur de lien</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main content -->
		<div class="content">
			<div class="card">
				<div class="card-header">
					<h5 class="card-title">Générer un lien d'accès</h5>
				</div>
				<div class="card-body">
					<p class="text-muted">Lien d'accès temporaire : Durée de validité = 1 usage</p>
					<form action="" method="POST">
						<input class="btn btn-outline-success" name="submit" type="submit" value="Générer un lien temporaire">
					</form>
					<?php echo $link; ?>
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<h5 class="card-title">Mes liens générés</h5>
				</div>
				<div class="card-body">
					<table id="table-notifs" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="col-md-10">Liens</th>
                                <th class="col-md-2">Création</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
							$query = $db->prepare('SELECT * FROM link_keys WHERE advisor_id = :advisor AND used = "false" ORDER BY id ASC');
							$query->execute(array(
								'advisor' => $_COOKIE['advisor']
							));
                            while ($data = $query->fetch()) {
                            	echo '<tr>
									<td>
										<div class="input-group input-group-sm">
											<input type="text" id="link' . $data['id'] . '" class="form-control" value="https://www.missionlocaledesgraves.fr/da-garantie-jeunes/?key=' . $data['link_key'] . '">
											<span class="input-group-append">
												<button type="button" class="btn btn-primary btn-flat" onclick="copy(' . $data['id'] . ')"><i class="fad fa-clipboard"></i></button>
											</span>
										</div>
									</td>
									<td>' . count_duration($data['created_date']) . '</td>
                            	</tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Liens</th>
                                <th>Création</th>
                            </tr>
                        </tfoot>
                    </table>
				</div>
			</div>
		</div>
	</div>
	<!-- Footer -->
	<footer class="main-footer">
		Administration Garantie Jeunes <strong>Copyright &copy; 2019 - <?php echo date('Y'); ?></strong>, tous droits réservés.
		<div class="float-right d-none d-sm-inline-block">
			<b>Version</b> <?php echo VERSION; ?>
		</div>
	</footer>
</div>
<!-- Scripts -->
<script src="./jscripts/jquery-3.4.1.min.js"></script>
<script src="./jscripts/sweetalert2.min.js"></script>
<script src="./jscripts/bootstrap.bundle.min.js"></script>
<script src="./jscripts/bootstrap.min.js"></script>
<!-- Alerts -->
<script src="./jscripts/toastr.min.js"></script>
<!-- Scripts -->
<script src="./jscripts/scripts.js"></script>
<script>
function copy($val) {
  /* Get the text field */
  var copyText = document.getElementById("link"+$val);
  /* Select the text field */
  copyText.select();
  /* Copy the text inside the text field */
  document.execCommand("copy");
  /* Alert the copied text */
  Swal.fire(
	'Lien copié !',
	'Le lien a été copié dans votre presse papier !',
	'info'
  );
}
</script>
</body>
</html>