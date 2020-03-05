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
// When an user is added on the list
if (isset($_POST['selectUser'])) {
	$query = $db->prepare('UPDATE groups SET red_list = "1" WHERE id = :id');
	$query->execute(array(
		'id' => $_POST['selectUser']
	));
	$query->closeCursor();
	$error = '<div class="alert alert-success"><i class="fad fa-save"></i> Changement effectué avec succès</div>';
}
// When an user is removed from the red list
if (isset($_GET['remove'])) {
	$query = $db->prepare('UPDATE groups SET red_list = "0" WHERE id = :id');
	$query->execute(array(
		'id' => $_GET['remove']
	));
	$query->closeCursor();
	unset($_GET['remove']);
	$error = '<div class="alert alert-success"><i class="fad fa-save"></i> Changement effectué avec succès</div>';
}
// When an user is added on the black list
if (isset($_GET['add_black'])) {
	$query = $db->prepare('UPDATE groups SET black_list = "1" WHERE id = :id');
	$query->execute(array(
		'id' => $_GET['add_black']
	));
	$query->closeCursor();
	unset($_GET['add_black']);
	$error = '<div class="alert alert-success"><i class="fad fa-save"></i> Changement effectué avec succès</div>';
}
// When an user is removed on the black list
if (isset($_GET['del_black'])) {
	$query = $db->prepare('UPDATE groups SET black_list = "0" WHERE id = :id');
	$query->execute(array(
		'id' => $_GET['del_black']
	));
	$query->closeCursor();
	unset($_GET['del_black']);
	$error = '<div class="alert alert-success"><i class="fad fa-save"></i> Changement effectué avec succès</div>';
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
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Liste Rouge</h1>
						<p class="m-0 text-muted">La liste rouge vous permet de bloquer la possibilité aux jeunes de faire leur déclaration d'activité. Il faut les débloquer ici avant qu'ils ne puissent de nouveau faire une déclaration d'activité.</p>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Liste Rouge</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main content -->
		<div class="content">
            <?php echo $error; ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des notifications</h3>
                </div>
                <div class="card-body">
                    <button class="mb-2 btn btn-success" data-toggle="modal" data-target="#addToList" title="Ajouter un jeune à la liste rouge"><i class="fad fa-plus"></i> Ajouter</button>
                    <table id="table-notifs" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="col-3">NOM</th>
                                <th class="col-3">Prénom</th>
                                <th class="col-2">Groupe</th>
                                <th class="col-1">Notification</th>
                                <th class="col-1">Blocage</th>
                                <th class="col-2">Supprimer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!isAdmin()) {
                                $query = $db->prepare('SELECT * FROM groups WHERE advisor_id = :advisor AND red_list = :isRed ORDER BY lastname ASC');
                                $query->execute(array(
                                    'advisor' => $_COOKIE['advisor'],
                                    'isRed' => "1"
                                ));
                            } else {
                                $query = $db->prepare('SELECT * FROM groups WHERE red_list = "1"');
                                $query->execute();
                            }
                            while ($data = $query->fetch()) {
                                echo '<tr>
                                <td>' . $data['lastname'] . '</td>
                                <td>' . $data['surname'] . '</td>
                                <td>' . $data['cohorte'] . '</td>
                                <td><span class="badge p-2 badge-pill badge-secondary">Auto</span></td>';
                                if ($data['black_list'] == '1' && isAdmin()) {
                                    echo '<td><i class="fad fa-lock-alt"></i> Jeune bloqué</td>';
                                    echo '<td></td>';
                                } elseif (!isAdmin()) {
                                    if ($data['black_list'] == '1') {
                                        echo '<td class="text-center"><a class="text-danger" href="?del_black=' . $data['id'] . '"><i class="fad fa-2x fa-lock-alt"></i></a></td>';
                                    } else {
                                        echo '<td class="text-center"><a class="text-secondary" href="?add_black=' . $data['id'] . '"><i class="fad fa-2x fa-lock-open-alt"></i></a></td>';
                                    }
                                    echo '<td><a class="text-danger" href="?remove=' . $data['id'] . '"><i class="fad fa-trash-alt"></i> Supprimer</a></td>';
                                } else {
                                    echo '<td></td>';
                                    echo '<td><a class="text-danger" href="?remove=' . $data['id'] . '"><i class="fad fa-trash-alt"></i> Supprimer</a></td>';
                                }
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>NOM</th>
                                <th>Prénom</th>
                                <th>Groupe</th>
                                <th>Notification</th>
                                <th>Blocage</th>
                                <th>Supprimer</th>
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
    $("#table-notifs").DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
}
</script>
<!-- Modal -->
<div id="addToList" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un jeune</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="selectUser">Sélectionner un jeune</label>
                        <select class="custom-select mr-sm-2" name="selectUser" id="selectUser">
                            <option selected>Choisir dans la liste</option>
                            <?php
                            if (!isAdmin()) {
                                $query = $db->prepare('SELECT * FROM groups WHERE advisor_id = :advisor AND active = "true" ORDER BY cohorte ASC');
                                $query->execute(array(
                                    'advisor' => $_COOKIE['advisor']
                                ));
                            } else {
                                $query = $db->prepare('SELECT * FROM groups ORDER BY cohorte ASC');
                                $query->execute();
                            }
                            while ($data = $query->fetch()) {
                                echo '<option value="' . $data['id'] . '">' . $data['lastname'] . ' ' . $data['surname'] . ' (' . $data['cohorte'] . ')</option>';
                            }
                            $query->closeCursor();
                            ?>
                        </select>
                    </div>
                    <input class="btn btn-primary" type="submit" name="submit">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>