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
?>
<!DOCTYPE html>
<html>
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
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark">Déclarations d'activité (Ce mois-ci)</h1>
							<p class="m-0 text-muted">Les déclarations d'activité du mois actuel sont listées ici. Le tableau peut se trier différemment en cliquant sur le titre de la colonne.</p>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
								<li class="breadcrumb-item active">Ce mois-ci</li>
							</ol>
						</div>
					</div>
				</div>
			</section>
			<!-- Main content -->
			<section class="content">
				<div class="accordion mb-3" id="accordionGroups">
					<?php
					$group_query = $db->prepare('SELECT * FROM cohortes WHERE active = "true"');
					$group_query->execute();
					$i = 1;
					while ($groups = $group_query->fetch()) {
					$group = $groups['name'];
					?>
					<div class="card shadow-none m-0">
						<div class="card-header p-1" id="heading<?php echo $i; ?>">
							<h2 class="mb-0">
								<button class="btn btn-link stretched-link" type="button" data-toggle="collapse" data-target="#collapse<?php echo $i; ?>" aria-expanded="true" aria-controls="collapse<?php echo $i; ?>">
									Cohorte <?php echo $group; ?>
								</button>
							</h2>
						</div>
						<div id="collapse<?php echo $i; ?>" class="collapse <?php if ($i === 1) {echo 'show';} ?>" aria-labelledby="heading<?php echo $i; ?>" data-parent="#accordionGroups">
							<div class="card-body">
								<table class="table table-bordered table-striped">
									<thead>
										<tr>
											<th class="col-3">NOM</th>
											<th class="col-3">Prénom</th>
											<th class="col-2">Groupe</th>
											<th class="col-2">Revenu</th>
											<th class="col-2">Consulter</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$surname = '';
										$lastname = '';
										if (!isAdmin()) {
											$query = $db->prepare("SELECT groups.lastname, groups.surname, groups.cohorte, groups.active, activity_check.id, activity_check.pay, activity_check.chk_interim, activity_check.update_date FROM groups LEFT JOIN activity_check ON groups.lastname = activity_check.lastname AND groups.surname = activity_check.surname WHERE groups.cohorte LIKE '%$group%' AND groups.advisor_id = :advisor AND active = 'true' ORDER BY groups.lastname ASC, update_date DESC");
											$query->execute(array(
												'advisor' => $_COOKIE['advisor']
											));
										} else {
											$query = $db->prepare("SELECT groups.lastname, groups.surname, groups.cohorte, groups.active, activity_check.id, activity_check.pay, activity_check.chk_interim, activity_check.update_date FROM groups LEFT JOIN activity_check ON groups.lastname = activity_check.lastname AND groups.surname = activity_check.surname WHERE groups.cohorte LIKE '%$group%' AND active = 'true' ORDER BY groups.lastname ASC, update_date DESC");
											$query->execute();
										}
										while ($data = $query->fetch()) {
											if ($surname != $data['surname'] && $lastname != $data['lastname'])  {
												?>
												<tr>
													<td><?php echo $data['lastname']; ?></td>
													<td><?php echo $data['surname']; ?></td>
													<td><?php echo $data['cohorte']; ?></td>
													<?php
													if ($data['pay'] == NULL || $data['update_date'] < date('Y-m-01 00:00:00')) {
														echo '<td><span class="badge badge-danger badge-pill">PAS DE DECLARATION</span></td>';
													} elseif ($data['chk_interim'] == 'true') {
														echo '<td><span class="text-info"><i class="fad fa-info-circle"></i> Interim coché</span></td>';
													} elseif ($data['pay'] == '' && $data['chk_interim'] == 'false') {
														echo '<td><span class="text-danger"><i class="fad fa-exclamation-triangle"></i> Montant manquant</span></td>';
													} else {
														echo '<td><span class="badge badge-warning badge-pill">'. $data['pay'] .' €</span></td>';
													}
													?>
													<td><a class="text-info" href="activity_check.php?show=<?php echo $data['id']; ?>">Consulter</a></td>
												</tr>
												<?php
												$surname = $data['surname'];
												$lastname = $data['lastname'];
											}
										}
										$query->closeCursor();
									?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<?php
					$i++;
					}
					$group_query->closeCursor();
					?>
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
	<!-- Scripts -->
	<script async src="./jscripts/lazysizes.min.js"></script>
	<script defer src="./jscripts/jquery-3.4.1.min.js"></script>
	<script defer src="./jscripts/sweetalert2.min.js"></script>
	<script defer src="./jscripts/bootstrap.bundle.min.js"></script>
	<script defer src="./jscripts/bootstrap.min.js"></script>
	<script defer src="./jscripts/scripts.js"></script>
</body>
</html>