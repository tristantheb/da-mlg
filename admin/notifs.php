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
if (isset($_POST['submit'])) {
	$query = $db->prepare('UPDATE notifications SET readed = "true" WHERE advisor_id = :advisor');
	$query->execute(array(
		'advisor' => $_COOKIE['advisor']
	));
	$query->closeCursor();
	$error = '<div class="alert alert-success">Liste remise à zéro !</div>';
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
// Notification is selected for remove
if (isset($_GET['remove'])) {
	$query = $db->prepare('UPDATE notifications SET readed = "true" WHERE id = :id');
	$query->execute(array(
		'id' => $_GET['remove']
	));
	$query->closeCursor();
	unset($_GET['remove']);
	$error = '<div class="alert alert-success">Notification fermée</div>';
}
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
						<h1 class="m-0 text-dark">Notifications</h1>
						<p class="m-0 text-muted">Ici s'affichent toutes les notifications que vous avez reçus concernant les jeunes en liste rouge.</p>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Notifications</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- Main content -->
		<section class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des notifications</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="table-da" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="col-6">NOM Prénom</th>
                                <th class="col-1">Groupe</th>
                                <th class="col-2">Heure</th>
                                <th class="col-1">Notification</th>
                                <th class="col-2"></th>
                            </tr>
                        </thead>
                        <tbody>
						<?php
						if (!isAdmin()) {
							$query = $db->prepare('SELECT * FROM notifications WHERE readed = "false" AND advisor_id = :advisor');
							$query->execute(array(
								'advisor' => $_COOKIE['advisor']
							));
						} else {
							$query = $db->prepare('SELECT * FROM notifications WHERE readed = "false"');
							$query->execute();
						}
						$start = date('H:i:s');
						$time = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($start)));
						while ($data = $query->fetch()) {
							echo '<tr>
							<td>' . $data['name'] . '</td>
							<td>' . $data['cohorte'] . '</td>
							<td>' . $data['time'] . '</td>';
							if ($data['time'] > $time) {
								echo '<td class="text-center"><span class="badge p-2 badge-success">Récente</span></td>';
							} else {
								echo '<td class="text-center"><span class="badge p-2 badge-secondary">Ancienne</span></td>';
							}
							echo '<td><a href="?remove='.$data['id'].'">Marquer comme lu</a></td>
							</tr>';
						}
						?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>NOM Prénom</th>
                                <th>Groupe</th>
                                <th>Heure</th>
                                <th>Notification</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
		</div>
	</section>
	<!-- Footer -->
	<footer class="main-footer">
		Administration Garantie Jeunes <strong>Copyright &copy; 2019 - <?php echo date('Y'); ?></strong>, tous droits réservés.
		<div class="float-right d-none d-sm-inline-block">
			<b>Version</b> <?php echo VERSION ?>
		</div>
	</footer>
</div>
<!-- Scripts -->
<script defer src="./jscripts/jquery-3.4.1.min.js"></script>
<script defer src="./jscripts/sweetalert2.min.js"></script>
<script defer src="./jscripts/bootstrap.bundle.min.js"></script>
<script defer src="./jscripts/bootstrap.min.js"></script>
<!-- DataTables -->
<script defer src="./jscripts/jquery.dataTables.min.js"></script>
<script defer src="./jscripts/dataTables.bootstrap4.min.js"></script>
<!-- Scripts -->
<script defer src="./jscripts/scripts.js"></script>
<script>
function showTable() {
    $("#table-da").DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "order": [[ 2, "desc" ]],
      "info": true,
      "autoWidth": false,
    });
}
</script>
</body>
</html>