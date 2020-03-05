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
		<?php
		if (!isset($_GET['view'])) {
		?>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Gestion groupes</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Gestion groupes</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main content -->
		<div class="content">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Menu contextuel</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item active">
                                    <a href="groups.php" class="nav-link">
                                        <i class="fad fa-tachometer-alt-fast"></i> Backstage
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="groups.php?view=folders" class="nav-link">
                                        <i class="fad fa-folder-tree"></i> Gérer les dossiers
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="groups.php?view=groups" class="nav-link">
                                        <i class="fad fa-users"></i> Gérer les groupes
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="groups.php?view=import" class="nav-link">
                                        <i class="fad fa-file-import"></i> Importer une cohorte
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <div class="col-md-9">
					<div class="info-box mb-3 bg-info">
						<span class="info-box-icon"><i class="fad fa-file"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Déclarations</span>
							<span class="info-box-number"><?php echo count_stats('activity_check', 'certif = "true"', true); ?></span>
						</div>
					</div>
					<div class="info-box mb-3 bg-success">
						<span class="info-box-icon"><i class="fad fa-folder"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Dossiers ouverts</span>
							<span class="info-box-number"><?php echo count_stats('groups', 'active = "true"'); ?></span>
						</div>
					</div>
					<div class="info-box mb-3 bg-danger">
						<span class="info-box-icon"><i class="fad fa-folder-open"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Dossiers fermés</span>
							<span class="info-box-number"><?php echo count_stats('groups', 'active = "false"'); ?></span>
						</div>
					</div>
                </div>
            </div>
		</div>
		<?php
		} elseif ($_GET['view'] == 'folders') {
		?>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Gestion jeune</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item"><a href="groups.php">Gestion groupes</a></li>
							<li class="breadcrumb-item active">Gestion jeune</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main content -->
		<div class="content">
			<div class="row">
				<div class="col-md-3">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">Menu contextuel</h3>
							<div class="card-tools">
								<button type="button" class="btn btn-tool" data-widget="collapse"><i class="fas fa-minus"></i></button>
							</div>
						</div>
						<div class="card-body p-0">
							<ul class="nav nav-pills flex-column">
								<li class="nav-item">
									<a href="groups.php" class="nav-link">
										<i class="fad fa-tachometer-alt-fast"></i> Backstage
									</a>
								</li>
								<li class="nav-item active">
									<a href="groups.php?view=folders" class="nav-link">
										<i class="fad fa-folder-tree"></i> Gérer les dossiers
									</a>
								</li>
								<li class="nav-item">
									<a href="groups.php?view=groups" class="nav-link">
										<i class="fad fa-users"></i> Gérer les groupes
									</a>
								</li>
								<li class="nav-item">
									<a href="groups.php?view=import" class="nav-link">
										<i class="fad fa-file-import"></i> Importer une cohorte
									</a>
								</li>
							</ul>
						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
				<div class="col-md-9">
				<?php
				if (!isset($_GET['id'])) {
					if (isset($_POST['submit'])) {
						$query = $db->prepare('UPDATE groups SET active="false" WHERE cohorte LIKE :group');
						$query->execute(array(
							'group' => '%'.$_POST['grpoff'].'%'
						));
						$query->closeCursor();
						echo '<div class="alert alert-success">Les jeunes ont été désactivés correctement.</div>';
					}
				?>
					<div class="card">
						<div class="card-header">Sélectionner un jeune</div>
						<div class="card-body">
							<div class="form-group">
								<select class="form-control" onchange="if (this.value) window.location.href=this.value">
									<option>Sélectionner un jeune</option>
									<?php
									$query = $db->prepare('SELECT * FROM groups');
									$query->execute();
									while ($data = $query->fetch()) {
										if ($data['active'] == "true") {
											echo '<option value="?view=folders&id='.$data['id'].'">'.$data['surname'].' '.$data['lastname'].' ('.$data['cohorte'].')</option>';
										} else {
											echo '<option value="?view=folders&id='.$data['id'].'" class="text-muted">'.$data['surname'].' '.$data['lastname'].' ('.$data['cohorte'].')</option>';
										}
									}
									$query->closeCursor();
									?>
								</select>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header">Désactiver les dossiers d'un groupe</div>
						<div class="card-body">
							<form action="" method="POST">
								<div class="form-group">
									<select class="form-control" name="grpoff">
										<option>Sélectionner un jeune</option>
										<?php
										$query = $db->prepare('SELECT * FROM cohortes WHERE active = "true"');
										$query->execute();
										while ($data = $query->fetch()) {
											echo '<option value="'.$data['name'].'">Groupe '.$data['name'].'</option>';
										}
										$query->closeCursor();
										?>
									</select>
								</div>
								<input class="btn btn-primary" type="submit" name="submit" value="Changer">
							</form>
						</div>
					</div>
				<?php
				} elseif (isset($_GET['id'])) {
					// Select folder content
					$query = $db->prepare('SELECT * FROM groups WHERE id = :id');
					$query->execute(array(
						'id' => $_GET['id']
					));
					$data = $query->fetch();
					// Select all advisors
					$adv_select = $db->prepare('SELECT * FROM advisors WHERE adm_lvl="2"');
					$adv_select->execute();
					$options = '<option>Sélectionnez un conseiller</option>';
					while ($adv_data = $adv_select->fetch()) {
						$select = '';
						if ($adv_data['advisor_id'] == $data['advisor_id']) {
							$select = 'selected';
						}
						$options .= '<option value="'.$adv_data['advisor_id'].'" '.$select.'>'.$adv_data['full_name'].'</option>';
					}
					$adv_select->closeCursor();
					// Form submit
					if (isset($_POST['submit'])) {
						$update = $db->prepare("UPDATE groups SET surname=:surname, lastname=:lastname, cohorte=:cohorte, advisor_id=:advisor, active=:active WHERE id = :id");
						$update->execute(array(
							'id' => $_GET['id'],
							'surname' => $_POST['surname'],
							'lastname' => $_POST['lastname'],
							'cohorte' => $_POST['cohorte'],
							'advisor' => $_POST['advisor_id'],
							'active' => $_POST['statut']
						));
						$update->closeCursor();
						echo '<div class="alert alert-success">Changement effectué, rechargez la page pour voir les modifications.</div>';
					}
					// Content liste
					echo '<div class="card">
						<div class="card-header">Modifier le dossier de <strong>'.$data['surname'].' '.$data['lastname'].'</strong></div>
						<div class="card-body">
							<form method="POST" action="">
								<div class="form-group">
									<label for="surname">Prénom</label>
									<input type="text" class="form-control" name="surname" id="surname" value="'.$data['surname'].'">
								</div>
								<div class="form-group">
									<label for="surname">NOM</label>
									<input type="text" class="form-control" name="lastname" id="lastname" value="'.$data['lastname'].'">
								</div>
								<div class="form-group">
									<label for="cohorte">Groupe</label>
									<input type="text" class="form-control" name="cohorte" id="cohorte" value="'.$data['cohorte'].'">
								</div>
								<div class="form-group">
									<label for="advisor_id">Conseiller</label>
									<select class="form-control" name="advisor_id" id="advisor_id">'.$options.'</select>
								</div>
								<hr>
								<div class="form-group">';
								if ($data['active'] == 'false') {
									echo '<div class="custom-control custom-radio">
										<input class="custom-control-input" type="radio" id="true" name="statut" value="false" checked>
										<label for="true" class="custom-control-label">Désactivé</label>
									</div>
									<div class="custom-control custom-radio">
										<input class="custom-control-input" type="radio" id="false" name="statut" value="true">
										<label for="false" class="custom-control-label">Activé</label>
									</div>';
								} else {
									echo '<div class="custom-control custom-radio">
										<input class="custom-control-input" type="radio" id="true" name="statut" value="false">
										<label for="true" class="custom-control-label">Désactivé</label>
									</div>
									<div class="custom-control custom-radio">
										<input class="custom-control-input" type="radio" id="false" name="statut" value="true" checked>
										<label for="false" class="custom-control-label">Activé</label>
									</div>';
								}
								echo '</div>
								<input type="submit" class="btn btn-primary" name="submit" value="Enregistrer">
							</form>
						</div>
					</div>';
					$query->closeCursor();
				}
				?>
				</div>
			</div>
		</div>
		<?php
		} elseif ($_GET['view'] == 'groups') {
		?>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Gestion groupe</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item"><a href="groups.php">Gestion groupes</a></li>
							<li class="breadcrumb-item active">Gestion groupe</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main content -->
		<div class="content">
			<div class="row">
				<div class="col-md-3">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">Menu contextuel</h3>
							<div class="card-tools">
								<button type="button" class="btn btn-tool" data-widget="collapse"><i class="fas fa-minus"></i></button>
							</div>
						</div>
						<div class="card-body p-0">
							<ul class="nav nav-pills flex-column">
								<li class="nav-item">
									<a href="groups.php" class="nav-link">
										<i class="fad fa-tachometer-alt-fast"></i> Backstage
									</a>
								</li>
								<li class="nav-item">
									<a href="groups.php?view=folders" class="nav-link">
										<i class="fad fa-folder-tree"></i> Gérer les dossiers
									</a>
								</li>
								<li class="nav-item active">
									<a href="groups.php?view=groups" class="nav-link">
										<i class="fad fa-users"></i> Gérer les groupes
									</a>
								</li>
								<li class="nav-item">
									<a href="groups.php?view=import" class="nav-link">
										<i class="fad fa-file-import"></i> Importer une cohorte
									</a>
								</li>
							</ul>
						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
				<div class="col-md-9">
					<?php
					if (isset($_POST['submit'])) {
						$query = $db->prepare('INSERT INTO cohortes VALUES ("", :number, "true")');
						$query->execute(array(
							'number' => $_POST['group_add']
						));
						$query->closeCursor();
						echo '<div class="alert alert-success">Groupe ajouté avec succès.</div>';
					}
					if (isset($_POST['disable'])) {
						$query = $db->prepare('UPDATE cohortes SET active="false" WHERE name = :number');
						$query->execute(array(
							'number' => $_POST['group_off']
						));
						$query->closeCursor();
						echo '<div class="alert alert-success">Groupe désactivé avec succès.</div>';
					}
					if (isset($_POST['delete'])) {
						$query = $db->prepare('DELETE FROM cohortes WHERE name = :number');
						$query->execute(array(
							'number' => $_POST['group_del']
						));
						$query->closeCursor();
						echo '<div class="alert alert-success">Groupe supprimé avec succès.</div>';
					}
					?>
					<div class="card card-outline card-success">
						<div class="card-header">Ajouter un groupe</div>
						<div class="card-body">
							<form method="POST" action="">
								<div class="form-group">
									<label for="group_add">Numéro du groupe</label>
									<input type="text" class="form-control" name="group_add" id="group_add" placeholder="Écrivez le numéro du groupe">
								</div>
								<input type="submit" class="btn btn-primary" name="submit" value="Envoyer">
							</form>
						</div>
					</div>
					<div class="card card-outline card-warning">
						<div class="card-header">Désactiver un groupe</div>
						<div class="card-body">
							<form method="POST" action="">
								<div class="form-group">
									<label for="group_off">Sélectionner un groupe</label>
									<select class="form-control" id="group_off" name="group_off">
										<option>Sélectionner un groupe</option>
										<?php
											$query = $db->prepare('SELECT * FROM cohortes WHERE active = "true"');
											$query->execute();
											while ($data = $query->fetch()) {
												echo '<option value="'.$data['name'].'">Groupe '.$data['name'].'</option>';
											}
											$query->closeCursor();
										?>
									</select>
								</div>
								<input type="submit" class="btn btn-primary" name="disable" value="Envoyer">
							</form>
						</div>
					</div>
					<div class="card card-outline card-danger">
						<div class="card-header">Supprimer un groupe</div>
						<div class="card-body">
							<form method="POST" action="">
								<div class="form-group">
									<label name="group_del">Sélectionner un groupe</label>
									<select class="form-control" name="group_del" name="group_del">
										<option>Sélectionner un groupe</option>
										<?php
											$query = $db->prepare('SELECT * FROM cohortes');
											$query->execute();
											while ($data = $query->fetch()) {
												echo '<option value="'.$data['name'].'">Groupe '.$data['name'].'</option>';
											}
											$query->closeCursor();
										?>
									</select>
								</div>
								<input type="submit" class="btn btn-primary" name="delete" value="Envoyer">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		} elseif ($_GET['view'] == 'import') {
		?>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Gestion groupes</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Gestion groupes</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main content -->
		<div class="content">
			<div class="row">
				<div class="col-md-3">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">Menu contextuel</h3>
							<div class="card-tools">
								<button type="button" class="btn btn-tool" data-widget="collapse"><i class="fas fa-minus"></i></button>
							</div>
						</div>
						<div class="card-body p-0">
							<ul class="nav nav-pills flex-column">
								<li class="nav-item">
									<a href="groups.php" class="nav-link">
										<i class="fad fa-tachometer-alt-fast"></i> Backstage
									</a>
								</li>
								<li class="nav-item">
									<a href="groups.php?view=folders" class="nav-link">
										<i class="fad fa-folder-tree"></i> Gérer les dossiers
									</a>
								</li>
								<li class="nav-item">
									<a href="groups.php?view=groups" class="nav-link">
										<i class="fad fa-users"></i> Gérer les groupes
									</a>
								</li>
								<li class="nav-item active">
									<a href="groups.php?view=import" class="nav-link">
										<i class="fad fa-file-import"></i> Importer une cohorte
									</a>
								</li>
							</ul>
						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
				<div class="col-md-9">
				<?php
				$query = $db->prepare('SELECT * FROM advisors WHERE adm_lvl="2"');
				$query->execute();
				$options = '<option>Sélectionnez un conseiller</option>';
				while ($data = $query->fetch()) {
					$options .= '<option value="'.$data['advisor_id'].'">'.$data['full_name'].'</option>';
				}
				$query->closeCursor();
				if (isset($_POST['submit-list'])) {
					$count = count($_POST['lastname']);
					$query_values = array();
					for($i=0; $i<$count; $i++){
						$c1 = $_POST['advisor'][$i];
						$c2 = $_POST['group'][$i];
						$c3 = $_POST['lastname'][$i];
						$c4 = $_POST['firstname'][$i];
						if(empty($c1) || empty($c2) || empty($c3) || empty($c4)) {
							$error .= "Le jeune".$c1." ".$c2." n'a pas toutes ses lignes complétées";
						} else {
							$query_values[] = " ('$c1','$c2','$c3','$c4') ";
						}
					}
					$values = implode(',', $query_values);
					$query = $db->prepare('INSERT INTO groups(advisor_id, cohorte, lastname, surname) VALUES'.$values);
					$query->execute();
					$query->closeCursor();
					$error = 'Success';
				}
				echo $error;
				?>
					<div class="card">
						<div class="card-header">Ajouter des jeunes</div>
						<div class="card-body">
							<button class="btn btn-success" id="add"><i class="fad fa-plus"></i> Ajouter</button>
							<form action="groups.php?view=import" method="POST">
								<table name="import-list" id="import">
									<thead>
										<tr>
											<th>NOM</th>
											<th>Prénom</th>
											<th>Groupe</th>
											<th>Conseiller</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><input class="form-control" name="lastname[]" type="text" placeholder="Nom du jeune"></td>
											<td><input class="form-control" name="firstname[]" type="text" placeholder="Prénom du jeune"></td>
											<td><input class="form-control" name="group[]" type="text" placeholder="Groupe du jeune"></td>
											<td><select class="form-control" name="advisor[]"><?php echo $options; ?></select></td>
										</tr>
									</tbody>
								</table>
								<input class="btn btn-primary float-right" type="submit" name="submit-list" value="Ajouter">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		}
		?>
	</div>
	<!-- Footer -->
	<footer class="main-footer">
		Administration Garantie Jeunes <strong>Copyright &copy; 2019 - <?php echo date('Y'); ?></strong>, tous droits réservés.
		<div class="float-right d-none d-sm-inline-block">
			<strong>Version</strong> <?php echo VERSION ?>
		</div>
	</footer>
</div>
<!-- Scripts -->
<script src="./jscripts/jquery-3.4.1.min.js"></script>
<script src="./jscripts/sweetalert2.min.js"></script>
<script src="./jscripts/bootstrap.bundle.min.js"></script>
<script src="./jscripts/bootstrap.min.js"></script>
<script src="./jscripts/scripts.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#add").click(function() {
          	$('#import tbody>tr:last').clone(true).insertAfter('#import tbody>tr:last');
          	return false;
        });
    });
</script>
</body>
</html>
