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


if (isset($_POST['submit'])) {
	// Get values for generating PDF	
	$query = $db->prepare('SELECT * FROM activity_check WHERE id = :id');
	$query->execute(array(
		'id' => $_GET['show']
	));
	$data = $query->fetch();
	// Get advisor full name
	$query = $db->prepare('SELECT full_name FROM advisors WHERE advisor_id = :id');
	$query->execute(array(
		'id' => $data['advisor_id']
	));
	$namer = $query->fetch();
	$query->closeCursor();
	// Call FPDF generator
	require_once 'includes/fpdf.php';
	// Add text for true/false value and get all string values from database
	if (!empty($data['work'])) {
		$activity_feed .= $data['work'] . "\n";
	}
	if (!empty($data['seminar'])) {
		$activity_feed .= $data['seminar'] . "\n";
	}
	if (!empty($data['steps'])) {
		$activity_feed .= $data['steps'] . "\n";
	}
	if (!empty($data['applications'])) {
		$activity_feed .= $data['applications'] . "\n";
	}
	if (!empty($data['education'])) {
		$activity_feed .= $data['education'] . "\n";
	}
	if (!empty($data['other'])) {
		$activity_feed .= $data['other'] . "\n";
	}
	if (!empty($data['nothing'])) {
		$activity_feed .= $data['nothing'] . "\n";
	}
	if(empty($activity_feed)) {
		$activity_feed = 'Pas d\'activité décrite';
	}
	$pay_feed = "";
	if ($data['chk_salaire'] == 'true') {
		$pay_feed = "un Salaire";
	}
	if ($data['chk_rsa'] == 'true') {
		$pay_feed = "du RSA";
	}
	if ($data['chk_ape'] == 'true') {
		$pay_feed = "Pôle emploi";
	}
	if ($data['chk_pri_a'] == 'true') {
		$pay_feed = "d'une Prime d'Activité";
	}
	if ($data['chk_aah'] == 'true') {
		$pay_feed = "l'AAH";
	}
	if ($data['chk_pen_a'] == 'true') {
		$pay_feed = "la Pension alimentaire";
	}
	if ($data['chk_rs'] == 'true') {
		$pay_feed = "d'un stage";
	}
	if ($data['chk_other'] == 'true') {
		$pay_feed = "d'un revenu autre";
	}

	$date = new DateTime($data['update_date']);
	// Clean cached content (Deleting this line can due bugs)
	ob_get_clean();
	// Start generating PDF, see FPDF documentation
	$pdf = new FPDF('P','mm', 'A4');
	$pdf->AddPage();
	$pdf->SetMargins(15, 10, 15);
	$pdf->Image('./c_images/garantie-jeunes.png',95,10,30,24);
	$pdf->Ln(30);
	$pdf->SetFont('Arial','',20);
	$pdf->SetTextColor(30, 30, 255);
	$pdf->Cell(190,5,"Ma déclaration d'activité du mois de ".$data['month']." ".$data['year']."");
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',12);
	$pdf->SetTextColor(0, 0, 0);
	$pdf->Write(6, "Au nom de ");
	# Set names
	$pdf->SetFont('Arial','B',18);
	$pdf->SetTextColor(30, 30, 255);
	$pdf->Write(6, $data['surname']." ".$data['lastname']);
	$pdf->Ln(8);
	$pdf->SetFont('Arial','B',14);
	$pdf->SetTextColor(0, 0, 0);
	$pdf->Write(6, "Au cours du mois précédent (que je déclare ici) :");
	$pdf->Ln(6);
	# Activity
	$pdf->SetFont('Arial','I',12);
	$pdf->Write(6, $data['activity']);
	$pdf->Ln(6);
	$pdf->SetFont('Arial','',12);
	$pdf->Write(6, $activity_feed);
	# Jump to position
	$pdf->SetY(110);
	# Add rect yellow
	$pdf->SetFillColor(255, 255, 0);
	$pdf->Rect(71, 109, 38, 7, 'F');
	# Pay
	$pdf->SetFont('Arial','B',14);
	$pdf->Write(6, "Je déclare avoir perçu : ".$data['pay']." euro(s)");
	$pdf->Ln(6);
	$pdf->SetFont('Arial','',12);
	$pdf->Write(6, "De : ".$pay_feed);
	$pdf->SetY(155);
	# Certify
	$pdf->Write(6, "Je soussigné(e) ".$data['surname']." ".$data['lastname'].",\natteste sur l'honneur l'exactiture des informations ci-dessus indiquées et suis informé(e) des sanctions encourues en cas de fausse déclaration.");
	$pdf->Ln(20);
	$pdf->Write(6, "Fait à : PESSAC (Conseiller)");
	$pdf->Ln(12);
	$pdf->Write(6, "Le : " . $date->format('d/m/Y à H:i:s'));
	$pdf->SetX(100);
	$pdf->Write(6, "Signature : ");
	$pdf->SetTextColor(255, 30, 30);
	$pdf->Write(6, "Formulaire validé par le jeune");
	$pdf->SetTextColor(0, 0, 0);
	# Complete
	$pdf->SetY(240);
	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(190,5,"Groupe ".$data['cohorte']." | Conseiller(ère) ".$namer['full_name']."",0,0,'C');
	$pdf->Image('./c_images/logo-mlg.png',45,250,40,25);
	$pdf->Image('./c_images/logoFSE.jpg',92,254,42,16);
	$pdf->Image('./c_images/ministere_travail.jpg',145,250,20,22);
	# Output
	$pdf->Output('I', 'result.pdf', true);
	$query->closeCursor();
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
						<h1 class="m-0 text-dark">Déclarations d'activité</h1>
						<p class="m-0 text-muted">Les déclarations d'activité sont listées ici. Le tableau peut se trier différemment en cliquant sur le titre de la colonne.</p>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Déclarations d'activité</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- Main content -->
		<section class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des déclarations d'activité</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
					<?php if (!isset($_GET['show'])) { ?>
                    <table id="table-da" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>NOM</th>
                                <th>Prénom</th>
                                <th>Groupe</th>
                                <th>Lien</th>
                                <th>Référent</th>
                                <th>Date</th>
                                <th>Consulter</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
						if (!isAdmin()) {
							$query = $db->prepare('SELECT activity_check.*, advisors.full_name FROM activity_check LEFT JOIN advisors ON activity_check.advisor_id = advisors.advisor_id WHERE activity_check.advisor_id = :advisor_id ORDER BY update_date DESC');
							$query->execute(array(
								'advisor_id' => $_COOKIE['advisor']
							));
						} else {
							$query = $db->prepare('SELECT activity_check.*, advisors.full_name FROM activity_check LEFT JOIN advisors ON activity_check.advisor_id = advisors.advisor_id  ORDER BY update_date DESC');
							$query->execute();
						}
                        while ($data = $query->fetch()) {
							if($data['update_date'] >= date('Y-m-01') && $data['update_date'] <= date('Y-m-31'))
								if ($data['certif'] == "true") {
									if ($data['type_key'] == 'internal') {
										$type_key = '<span class="badge badge-primary">Interne</span>';
									} elseif ($data['type_key'] == 'external') {
										$type_key = '<span class="badge badge-success">Externe</span>';
									} elseif ($data['type_key'] == 'advisor') {
										$type_key = '<span class="badge badge-danger">Conseiller</span>';
									}
								echo '<tr>
									<td>'.$data['lastname'].'</td>
									<td>'.$data['surname'].'</td>
									<td>'.$data['cohorte'].'</td>
									<td>'.$type_key.'</td>
									<td>'.$data['full_name'].'</td>
									<td>'.$data['update_date'].'</td>
									<td><a class="text-info" href="?show='.$data['id'].'">Consulter</a></td>
								</tr>';
								}
                        }
                        $query->closeCursor();
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>NOM</th>
                                <th>Prénom</th>
                                <th>Groupe</th>
                                <th>Lien</th>
                                <th>Référent</th>
                                <th>Date</th>
                                <th>Consulter</th>
                            </tr>
                        </tfoot>
                    </table>
					<?php } else {
						$query = $db->prepare('SELECT * FROM activity_check WHERE id = :id');
						$query->execute(array(
							'id' => $_GET['show']
						));
						$data = $query->fetch();
						echo '<h4 class="card-header">Déclaration d\'activité de '.$data['surname'].' '.$data['lastname'].' <span class="badge badge-pill badge-info">Cohorte '.$data['cohorte'].'</span></h4>
							<div class="card-body">
								<h5 class="card-title">Activité au mois de '.$data['month'].' '.$data['year'].'</h5>
								<p class="card-text">Le jeune a déclaré « <strong>'.$data['activity'].'</strong> »</p>
							</div>
							<ul class="list-group list-group-flush">';
						if (!empty($data['work'])) {
							echo '<li class="list-group-item"><b>J\'ai travaillé à/chez :</b><br>'.$data['work'].'</li>';
						}
						if (!empty($data['seminar'])) {
							echo '<li class="list-group-item"><b>J\'ai effectué des démarches auprès des entreprises :</b><br>'.$data['seminar'].'</li>';
						}
						if (!empty($data['steps'])) {
							echo '<li class="list-group-item"><b>J\'ai déposé des CVs chez :</b><br>'.$data['steps'].'</li>';
						}
						if (!empty($data['applications'])) {
							echo '<li class="list-group-item"><b>J\'ai été en formation :</b><br>'.$data['applications'].'</li>';
						}
						if (!empty($data['education'])) {
							echo '<li class="list-group-item"><b>J\'ai été en formation :</b><br>'.$data['education'].'</li>';
						}
						if (!empty($data['other'])) {
							echo '<li class="list-group-item"><b>J\'ai effectué :</b><br>'.$data['other'].'</li>';
						}
						if (!empty($data['nothing'])) {
							echo '<li class="list-group-item"><b>Je n\'ai rien effectué car :</b><br>'.$data['nothing'].'</li>';
						}
						echo '</ul>
							<div class="card-body">
								<h5>Je déclare avoir perçu</h5>
								<p>Durant le mois, j\'ai perçu <span class="badge p-2 badge-warning">'.$data['pay'].' €</span> de<br>';
						if ($data['chk_interim'] == 'true' && !isAdmin()) {
							echo '<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> Déclaration incomplète</span> • <a href="activity_edit.php?id='.$data['id'].'">Éditer</a>';
						} elseif (isAdmin()) {
							echo '<a href="activity_edit.php?id='.$data['id'].'"><i class="fad fa-edit"></i> Éditer</a>';
						}
						echo '</p>';
						if ($data['chk_salaire'] == 'true') {
							echo '<span class="badge badge-secondary">Non</span> <span class="badge badge-success">Oui</span> d\'un salaire<br>';
						} else {
							echo '<span class="badge badge-danger">Non</span> <span class="badge badge-secondary">Oui</span> d\'un salaire<br>';
						}
						if ($data['chk_rsa'] == 'true') {
							echo '<span class="badge badge-secondary">Non</span> <span class="badge badge-success">Oui</span> du RSA<br>';
						} else {
							echo '<span class="badge badge-danger">Non</span> <span class="badge badge-secondary">Oui</span> du RSA<br>';
						}
						if ($data['chk_ape'] == 'true') {
							echo '<span class="badge badge-secondary">Non</span> <span class="badge badge-success">Oui</span> de pôle emploi<br>';
						} else {
							echo '<span class="badge badge-danger">Non</span> <span class="badge badge-secondary">Oui</span> de pôle emploi<br>';
						}
						if ($data['chk_pri_a'] == 'true') {
							echo '<span class="badge badge-secondary">Non</span> <span class="badge badge-success">Oui</span> d\'une Prive d\'Activité<br>';
						} else {
							echo '<span class="badge badge-danger">Non</span> <span class="badge badge-secondary">Oui</span> d\'une Prive d\'Activité<br>';
						}
						if ($data['chk_aah'] == 'true') {
							echo '<span class="badge badge-secondary">Non</span> <span class="badge badge-success">Oui</span> de l\'AAH<br>';
						} else {
							echo '<span class="badge badge-danger">Non</span> <span class="badge badge-secondary">Oui</span> de l\'AAH<br>';
						}
						if ($data['chk_pen_a'] == 'true') {
							echo '<span class="badge badge-secondary">Non</span> <span class="badge badge-success">Oui</span> de la pension alimentaire<br>';
						} else {
							echo '<span class="badge badge-danger">Non</span> <span class="badge badge-secondary">Oui</span> de la pension alimentaire<br>';
						}
						if ($data['chk_rs'] == 'true') {
							echo '<span class="badge badge-secondary">Non</span> <span class="badge badge-success">Oui</span> d\'un stage<br>';
						} else {
							echo '<span class="badge badge-danger">Non</span> <span class="badge badge-secondary">Oui</span> d\'un stage<br>';
						}
						if ($data['chk_other'] == 'true') {
							echo '<span class="badge badge-secondary">Non</span> <span class="badge badge-success">Oui</span> d\'un revenu autre<br>';
						} else {
							echo '<span class="badge badge-danger">Non</span> <span class="badge badge-secondary">Oui</span> d\'un revenu autre<br>';
						}
						echo'<hr><a href="activity_check.php" class="btn btn-sm btn-primary float-right">Retourner à la liste</a>
							<form action="" method="post"><button type="submit" name="submit" class="btn btn-sm btn-secondary" value="true"><i class="far fa-file-pdf"></i> Générer un PDF</button></form>
							</div>';
						$query->closeCursor();
					} ?>
                </div>
            </div>
		</section>
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
<script async src="./jscripts/lazysizes.min.js"></script>
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
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "order": [[ 6, "desc" ]],
      "info": true,
      "autoWidth": false
    });
}
</script>
</body>
</html>