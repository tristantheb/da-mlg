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
	<?php if (!isset($_GET['url'])) { ?>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Gestion membres</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Gestion membres</li>
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
									<a href="advisors.php" class="nav-link">
										<i class="fad fa-tachometer-alt-fast"></i> Gérer les conseillers
									</a>
								</li>
								<li class="nav-item">
									<a href="advisors.php?url=add" class="nav-link">
										<i class="fad fa-folder-tree"></i> Ajouter un conseiller
									</a>
								</li>
								<li class="nav-item">
									<a href="advisors.php?url=status" class="nav-link">
										<i class="fad fa-users"></i> Désactiver un conseiller
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
					if (isset($_GET['show'])) {
						$query = $db->prepare('SELECT * FROM advisors WHERE advisor_id = :show');
						$query->execute(array(
							'show' => $_GET['show']
						));
						$data = $query->fetch();
						$query->closeCursor();
						if (isset($_POST['submit_edit'])) {
							if(!empty($_POST['password'])) {
								$password = md5($_POST['password']);
							} else {
								$password = $data['password'];
							}
							if (isset($_POST['surname']) && isset($_POST['lastname']) && isset($_POST['rank'])) {
								$query = $db->prepare('UPDATE advisors SET lastaname = :lastname, surname = :surname, full_name = :full_name, password = :pass, adm_lvl = :adm_lvl WHERE advisor_id = :show');
								$query->execute(array(
									'surname' => $_POST['surname'],
									'lastname' => $_POST['lastname'],
									'full_name' => $_POST['surname'] .' '. $_POST['lastname'],
									'pass' => $password,
									'adm_lvl' => $_POST['rank'],
									'show' => $_GET['show']
								));
								$query->closeCursor();
								$error = '<div class="alert alert-success"><i class="fa fa-check-circle"></i> L\'utilisateur a été modifié avec succès</div>';
							}
						}
					?>
					<div class="card">
						<div class="card-body">
							<form action="" method="POST">
								<h4 class="display-4">Modifier un utilisateur</h4>
								<?php echo $error; ?>
								<h4>Informations principales</h4>
								<div class="form-group">
									<label for="surname">Prénom</label>
									<input class="form-control" type="text" id="surname" name="surname" placeholder="Entrer le prénom" value="<?php echo $data['surname']; ?>">
								</div>
								<div class="form-group">
									<label for="lastname">Nom</label>
									<input class="form-control" type="text" id="lastname" name="lastname" placeholder="Entrer le nom" onkeyup="this.value=this.value.toUpperCase();" value="<?php echo $data['lastname']; ?>">
								</div>
								<div class="form-group">
									<label for="password">Mot de passe</label>
									<input class="form-control" type="password" id="password" name="password" placeholder="Entrer le mot de passe">
								</div>
								<h4>Droits</h4>
								<div class="custom-control custom-radio">
									<input type="radio" id="administrator" name="rank" class="custom-control-input" value="1" <?php if ($data['adm_lvl'] === '1') { echo 'checked';} ?>>
									<label class="custom-control-label" for="administrator">Administrateur</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" id="advisor" name="rank" class="custom-control-input" value="2" <?php if ($data['adm_lvl'] === '2') { echo 'checked';} ?>>
									<label class="custom-control-label" for="advisor">Conseiller</label>
								</div>
								<button type="submit" name="submit_edit" class="btn btn-primary float-right">Valider</button>
							</form>
						</div>
					</div>
					<?php } else { ?>
					<div class="card">
						<div class="card-header">Gestion membres</div>
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>NOM</th>
									<th>Prénom</th>
									<th style="width: 150px;">Consulter</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$query = $db->prepare('SELECT * FROM advisors ORDER BY advisor_id DESC');
								$query->execute();
								while ($data = $query->fetch()) {
									if ($data['full_name'] == "Service CIVIQUE") {
										echo '<tr>
											<td>'.$data['surname'].'</td>
											<td>'.$data['lastname'].'</td>
											<td><span class="text-muted"><i class="fa fa-user-lock"></i> Bloqué</span></td>
										</tr>';
									} else {
										echo '<tr>
											<td>'.$data['surname'].'</td>
											<td>'.$data['lastname'].'</td>
											<td><a class="text-info" href="?show='.$data['advisor_id'].'"><i class="fa fa-user-cog"></i> Consulter</a></td>
										</tr>';
									}
								}
								?>
							</tbody>
						</table>
					</div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	<?php
	} elseif ($_GET['url'] === 'add') {
	if (isset($_POST['submit_add'])) {
		if (isset($_POST['surname']) && isset($_POST['lastname']) && isset($_POST['password']) && isset($_POST['rank'])) {
			$query = $db->prepare('INSERT INTO advisors (full_name, surname, lastname, password, adm_lvl) VALUES(:full_name, :surname, :lastname, :password, :rank)');
			$query->execute(array(
				'surname' => $_POST['surname'],
				'lastname' => $_POST['lastname'],
				'full_name' => $_POST['surname'] .' '. $_POST['lastname'],
				'password' => md5($_POST['password']),
				'rank' => $_POST['rank']
			));
			$query->closeCursor();
			$error = '<div class="alert alert-success"><i class="fa fa-check-circle"></i> L\'utilisateur a été ajouté avec succès</div>';
		}
	}
	?>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Ajouter membres</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item"><a href="advisors.php">Gestion membres</a></li>
							<li class="breadcrumb-item active">Ajouter membres</li>
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
									<a href="advisors.php" class="nav-link">
										<i class="fad fa-tachometer-alt-fast"></i> Gérer les conseillers
									</a>
								</li>
								<li class="nav-item active">
									<a href="advisors.php?url=add" class="nav-link">
										<i class="fad fa-folder-tree"></i> Ajouter un conseiller
									</a>
								</li>
								<li class="nav-item">
									<a href="advisors.php?url=status" class="nav-link">
										<i class="fad fa-users"></i> Désactiver un conseiller
									</a>
								</li>
							</ul>
						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
				<div class="col-md-9">
					<div class="card">
						<div class="card-body">
							<form action="" method="POST">
								<h4 class="display-4">Ajouter un utilisateur</h4>
								<?php echo $error; ?>
								<h4>Informations principales</h4>
								<div class="form-group">
									<label for="surname">Prénom</label>
									<input class="form-control" type="text" id="surname" name="surname" placeholder="Entrer le prénom">
								</div>
								<div class="form-group">
									<label for="lastname">Nom</label>
									<input class="form-control" type="text" id="lastname" name="lastname" placeholder="Entrer le nom" onkeyup="this.value=this.value.toUpperCase();">
								</div>
								<div class="form-group">
									<label for="password">Mot de passe</label>
									<input class="form-control" type="password" id="password" name="password" placeholder="Entrer le mot de passe">
								</div>
								<h4>Droits</h4>
								<div class="custom-control custom-radio">
									<input type="radio" id="administrator" name="rank" class="custom-control-input" value="1">
									<label class="custom-control-label" for="administrator">Administrateur</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" id="advisor" name="rank" class="custom-control-input" value="2">
									<label class="custom-control-label" for="advisor">Conseiller</label>
								</div>
								<button type="submit" name="submit_add" class="btn btn-primary float-right">Valider</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	} elseif ($_GET['url'] === 'status') {
	if (isset($_GET['enable'])) {
		$query = $db->prepare('UPDATE advisors SET active = "true" WHERE advisor_id = :id');
		$query->execute(array(
			'id' => $_GET['enable']
		));
		$query->closeCursor();
		$error = '<div class="alert alert-success"><i class="fa fa-check-circle"></i> L\'utilisateur a été activé avec succès</div>';
	} elseif (isset($_GET['disable'])) {
		$query = $db->prepare('UPDATE advisors SET active = "false" WHERE advisor_id = :id');
		$query->execute(array(
			'id' => $_GET['disable']
		));
		$query->closeCursor();
		$error = '<div class="alert alert-success"><i class="fa fa-check-circle"></i> L\'utilisateur a été désactivé avec succès</div>';
	}
	echo $error;
	?>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Activer/Désactiver</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item"><a href="advisors.php">Gestion membres</a></li>
							<li class="breadcrumb-item active">Activer/Désactiver</li>
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
									<a href="advisors.php" class="nav-link">
										<i class="fad fa-tachometer-alt-fast"></i> Gérer les conseillers
									</a>
								</li>
								<li class="nav-item">
									<a href="advisors.php?url=add" class="nav-link">
										<i class="fad fa-folder-tree"></i> Ajouter un conseiller
									</a>
								</li>
								<li class="nav-item active">
									<a href="advisors.php?url=status" class="nav-link">
										<i class="fad fa-users"></i> Désactiver un conseiller
									</a>
								</li>
							</ul>
						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
				<div class="col-md-9">
					<div class="card">
						<div class="card-header">Gestion membres</div>
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th scope="col">NOM</th>
									<th scope="col">Prénom</th>
									<th scope="col" style="width: 150px;">État</th>
								</tr>
							</thead>
							<tbody>
							<?php
							$query = $db->prepare('SELECT * FROM advisors ORDER BY advisor_id DESC');
							$query->execute();
							while ($data = $query->fetch()) {
								if ($data['full_name'] == "Service CIVIQUE") {
									echo '<tr>
										<td>'.$data['surname'].'</td>
										<td>'.$data['lastname'].'</td>
										<td><span class="text-muted"><i class="fa fa-user-shield"></i> Protégé</span></td>
									</tr>';
								} else {
									echo '<tr>
										<td>'.$data['surname'].'</td>
										<td>'.$data['lastname'].'</td>';
									if ($data['active'] === "true") {
										echo '<td><span class="text-muted"><i class="fa fa-user-check"></i></span> | <a class="text-danger" href="?url=status&disable='.$data['advisor_id'].'"><i class="fa fa-user-times"></i></a></td>';
									} else {
										echo '<td><a class="text-success" href="?url=status&enable='.$data['advisor_id'].'"><i class="fa fa-user-check"></i></a> | <span class="text-muted"><i class="fa fa-user-times"></i></span></td>';
									}
									echo '</tr>';
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
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
</body>
</html>
