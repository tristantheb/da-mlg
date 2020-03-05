<?php
require_once "./includes/config.php";
// Check session
if (!isLogged() && !isIdentified()) {
	header('Location: ./login.php');
} elseif (!isLogged() && isIdentified()) {
	header('Location: ./lockscreen.php');
}
// Init $error
if (empty($error)) {
	$error = '';
}
// If session ok - Start site load
include_once "./includes/function_stats.php";
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
?>
<!DOCTYPE html>
<html>
<?php include_once "./includes/tmpl_head.php"; ?>
<body class="hold-transition sidebar-mini">
<?php if($data['first_logon'] == '0') { ?>
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
						<h1 class="m-0 text-dark">Tableau de bord</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Tableau de bord</li>
						</ol>
					</div>
				</div>
				<?php include_once "./includes/tmpl_alerts.php"; ?>
			</div>
			<div class="row">
				<div class="col-md-3 col-sm-6 col-12">
					<div class="info-box">
					<span class="info-box-icon bg-info"><i class="fad fa-folder"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Dossiers (global)</span>
						<span class="info-box-number"><?php echo count_stats('groups', 'active = "true"'); ?></span>
					</div>
					<!-- /.info-box-content -->
					</div>
					<!-- /.info-box -->
				</div>
				<!-- /.col -->
				<div class="col-md-3 col-sm-6 col-12">
					<div class="info-box">
					<span class="info-box-icon bg-success"><i class="fad fa-copy"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Déclarations (global)</span>
						<span class="info-box-number"><?php echo count_stats('activity_check', 'certif = "true"', true); ?></span>
					</div>
					<!-- /.info-box-content -->
					</div>
					<!-- /.info-box -->
				</div>
				<!-- /.col -->
				<div class="col-md-3 col-sm-6 col-12">
					<div class="info-box">
					<span class="info-box-icon bg-warning"><i class="fad fa-bell"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Notifications (global)</span>
						<span class="info-box-number"><?php echo count_stats('notifications', 'readed = "false"'); ?></span>
					</div>
					<!-- /.info-box-content -->
					</div>
					<!-- /.info-box -->
				</div>
				<!-- /.col -->
				<div class="col-md-3 col-sm-6 col-12">
					<div class="info-box">
					<span class="info-box-icon bg-danger"><i class="fad fa-flag"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Liste rouge (global)</span>
						<span class="info-box-number"><?php echo count_stats('groups', 'red_list = "1"'); ?></span>
					</div>
					<!-- /.info-box-content -->
					</div>
					<!-- /.info-box -->
				</div>
				<!-- /.col -->
			</div>
		</div>
		<!-- Main content -->
		<div class="content">
			<div class="container-fluid">
				<div class="col">
					<div class="card">
						<div class="card-header border-0">
							<div class="d-flex justify-content-between">
								<h3 class="card-title">Déclarations remplies par jour</h3>
							</div>
						</div>
						<div class="card-body">
							<div class="d-flex">
								<p class="d-flex flex-column">
									<span class="text-bold text-lg"><?php echo number_format(count_stats('activity_check', 'certif = "true"', true) / count_stats('groups', 'active = "true"') * 100, 2, ',', ' '); ?>%</span>
									<span>Total remplies</span>
								</p>
								<p class="ml-auto d-flex flex-column text-right">
									<?php echo $percent; ?>
									<span class="text-muted">Depuis hier</span>
								</p>
							</div>
							<div class="position-relative">
								<canvas id="chart-DA" height="100"></canvas>
							</div>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card">
						<?php if (!isAdmin()) { ?>
						<div class="card-header border-0">
							<div class="d-flex justify-content-between">
								<h3 class="card-title">Mes déclarations remplies par jour</h3>
							</div>
						</div>
						<div class="card-body">
							<div class="d-flex">
								<p class="d-flex flex-column">
									<span class="text-bold text-lg"><?php echo count_stats('activity_check', 'certif = "true" AND advisor_id = "'.$_COOKIE['advisor'].'" AND update_date >= "'.date('Y-m-01 00:00:00').'" AND update_date <= "'.date('Y-m-31 23:59:59').'"', true) . "/" . count_stats('groups', 'active = "true" AND advisor_id = "'.$_COOKIE['advisor'].'"'); ?></span>
									<span>Total remplies</span>
								</p>
								<p class="ml-auto d-flex flex-column text-right">
									<?php echo $percent_me; ?>
									<span class="text-muted">Depuis hier</span>
								</p>
							</div>
							<div class="position-relative">
								<canvas id="chart-DA-me" height="100"></canvas>
							</div>
						</div>
						<?php } ?>
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
<?php } else { ?>
<div class="wrapper">
	<!-- Navigation -->
	<?php include_once "includes/tmpl_nav.php"; ?>
	<!-- Main Sidebar Container -->
	<aside class="main-sidebar sidebar-dark-primary elevation-4">
		<!-- Brand Logo -->
		<a href="index.php" class="brand-link">
			<img src="./c_images/logo-gj.png" alt="AdminDA" class="brand-image img-circle elevation-3" style="opacity: .8">
			<span class="brand-text font-weight-light"><strong>Admin</strong>DA</span>
		</a>
		<!-- start:Sidebar -->
		<div class="sidebar">
			<!-- Sidebar user panel (optional) -->
			<div class="user-panel mt-3 pb-3 mb-3 d-flex">
				<div class="image">
					<img src="./c_images/user.png" class="img-circle elevation-2" alt="Profile">
				</div>
				<div class="info">
					<a href="settings.php" class="d-block"><?php echo $data['full_name']; ?></a>
				</div>
			</div>
		</div>
		<!-- end:Sidebar -->
	</aside>
	<!-- Contenu principal -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Première connexion</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item active">Première connexion</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- Main content -->
		<section class="content">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Changement de mot de passe</h3>
				</div>
				<div class="card-body">
					<?php
					if (isset($_POST['submit'])) {
						if (empty($_POST['old_pass']) || empty($_POST['new_pass']) || empty($_POST['confirm_pass'])){
							$error = '<div class="alert alert-danger">Une case est vide.</div>';
						} elseif ($_POST['old_pass'] == $_POST['new_pass']) {
							$error = '<div class="alert alert-danger">Le nouveau mot de passe est identique à l\'ancien.</div>';
						} elseif ($_POST['new_pass'] != $_POST['confirm_pass']) {
							$error = '<div class="alert alert-danger">Le mot de passe ne correspond pas à sa confirmation.</div>';
						} else {
							$query = $db->prepare("UPDATE advisors SET password=:password, first_logon='0' WHERE advisor_id=:id");
							$query->execute(array(
								'id' => $_COOKIE['advisor'],
								'password' => md5($_POST['new_pass'])
							));
							$query->closeCursor();
							header('Location: logout.php');
						}
					}
					echo $error;
					?>
					<form action="" method="POST">
						<div class="form-group">
							<label for="old_pass">Ancien mot de passe :</label>
							<div class="input-group flex-nowrap">
								<div class="input-group-prepend">
									<span class="input-group-text" id="addon-wrapping"><i class="fa fa-lock"></i></span>
								</div>
								<input type="password" id="old_pass" name="old_pass" class="form-control" placeholder="Ancien mot de passe" aria-describedby="addon-wrapping">
							</div>
						</div>
						<div class="form-group">
							<label for="new_pass">Nouveau mot de passe :</label>
							<div class="input-group flex-nowrap">
								<div class="input-group-prepend">
									<span class="input-group-text" id="addon-wrapping"><i class="fa fa-lock"></i></span>
								</div>
								<input type="password" id="new_pass" name="new_pass" class="form-control" placeholder="Nouveau mot de passe" aria-describedby="addon-wrapping">
							</div>
						</div>
						<div class="form-group">
							<label for="confirm_pass">Répéter le mot de passe :</label>
							<div class="input-group flex-nowrap">
								<div class="input-group-prepend">
									<span class="input-group-text" id="addon-wrapping"><i class="fa fa-lock"></i></span>
								</div>
								<input type="password" id="confirm_pass" name="confirm_pass" class="form-control" placeholder="Répéter le mot de passe" aria-describedby="addon-wrapping">
							</div>
						</div>
						<input type="submit" id="submit" name="submit" class="btn btn-success float-right" value="Enregistrer">
					</form>
				</div>
			</div>
			<div class="alert alert-info">
				<p class="m-0"><i class="fad fa-info-circle"></i> Vous serez déconnecté lors du changement de mot de passe afin de correctement vous connecter avec le nouveau !</p>
			</div>
		</section>
	</div>
	<!-- Footer -->
	<footer class="main-footer">
		Administration Garantie Jeunes <strong>Copyright &copy; 2019 - <?php echo date('Y'); ?></strong>, tous droits réservés.
		<div class="float-right d-none d-sm-inline-block">
			<b>Version</b> <?php echo VERSION ?>
		</div>
	</footer>
</div>
<?php } ?>
<!-- Scripts -->
<script async src="./jscripts/lazysizes.min.js"></script>
<script defer src="./jscripts/jquery-3.4.1.min.js"></script>
<script defer src="./jscripts/sweetalert2.min.js"></script>
<script defer src="./jscripts/bootstrap.bundle.min.js"></script>
<script defer src="./jscripts/bootstrap.min.js"></script>
<!-- Chart -->
<script defer src="./jscripts/chart.min.js"></script>
<!-- Scripts -->
<script defer src="./jscripts/scripts.js"></script>
<script>
function showChart() {
	var chart = document.getElementById('chart-DA');
	var chartDA = new Chart(chart, {
		type: 'line',
		data: {
			labels: ['Jour 1', 'Jour 2', 'Jour 3', 'Jour 4', 'Jour 5', 'Jour 6', 'Jour 7', 'Jour 8', 'Jour 9', 'Jour 10', 'Jour 11', 'Jour 12', 'Jour 13', 'Jour 14', 'Jour 15', 'Jour 16', 'Jour 17', 'Jour 18', 'Jour 19', 'Jour 20', 'Jour 21', 'Jour 22', 'Jour 23', 'Jour 24', 'Jour 25', 'Jour 26', 'Jour 27', 'Jour 28', 'Jour 29', 'Jour 30', 'Jour 31'],
			datasets: [{
				label: 'Nombre de déclarations',
				data: [<?php echo $json; ?>],
				borderColor: 'rgba(23, 162, 184, 1)',
				borderWidth: 3,
				fill: false
			},
			{
				label: 'Nombre de déclarations (Hors période)',
				data: [NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, <?php echo $json_hs; ?>],
				borderColor: 'rgba(253, 126, 20, 1)',
				borderWidth: 3,
				fill: false
			}]
		},
		options: {
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true
					}
				}]
			}
		}
	});
	<?php if (!isAdmin()) { ?>
	var chart_me = document.getElementById('chart-DA-me');
	var chartDA = new Chart(chart_me, {
		type: 'line',
		data: {
			labels: ['Jour 1', 'Jour 2', 'Jour 3', 'Jour 4', 'Jour 5', 'Jour 6', 'Jour 7', 'Jour 8', 'Jour 9', 'Jour 10', 'Jour 11', 'Jour 12', 'Jour 13', 'Jour 14', 'Jour 15', 'Jour 16', 'Jour 17', 'Jour 18', 'Jour 19', 'Jour 20', 'Jour 21', 'Jour 22', 'Jour 23', 'Jour 24', 'Jour 25', 'Jour 26', 'Jour 27', 'Jour 28', 'Jour 29', 'Jour 30', 'Jour 31'],
			datasets: [{
				label: 'Nombre de déclarations',
				data: [<?php echo $json; ?>],
				borderColor: 'rgba(23, 162, 184, 1)',
				borderWidth: 3,
				fill: false
			},
			{
				label: 'Nombre de déclarations (Hors période)',
				data: [NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, NaN, <?php echo $json_hs; ?>],
				borderColor: 'rgba(253, 126, 20, 1)',
				borderWidth: 3,
				fill: false
			}]
		},
		options: {
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true
					}
				}]
			}
		}
	});
	<?php } ?>
}
</script>
</body>
</html>