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
                        <h1 class="m-0 text-dark">Rechercher</h1>
						<p class="m-0 text-muted">Rechercher un jeune pour retrouver ses déclarations d'activité.</p>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
                            <li class="breadcrumb-item active">Rechercher</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <!-- Main content -->
        <section class="content">
            <?php
            /**
             * Search system switch
             */
            if (isset($_GET['type'])) {
                $type = $_GET['type'];
            } else {
                header('Localtion: search.php');
            }
            switch ($type) {
            case "search":
                # When is on search mode, show the list
                echo '<table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Prénom</th>
                            <th scope="col">NOM</th>
                            <th scope="col"><i class="fa fa-users"></i> Cohorte</th>
                            <th scope="col"><i class="fa fa-money-check-alt"></i> Revenu déclaré</th>
                            <th scope="col"><i class="fa fa-clock"></i> Date de déclaration</th>
                            <th scope="col"><i class="fa fa-file-alt"></i> Fiche du jeune</th>
                        </tr>
                    </thead>
                    <tbody>';
                // Query search for found user and group list
                if (isAdmin()) {
                    $query = $db->prepare("SELECT * FROM activity_check WHERE (lastname LIKE :search OR surname LIKE :search OR cohorte LIKE :search) ORDER BY id");
                    $query->execute(array(
                        'search' => '%' . $_GET['search'] . '%'
                    ));
                } else {
                    $query = $db->prepare("SELECT * FROM activity_check WHERE (lastname LIKE :search OR surname LIKE :search OR cohorte LIKE :search) AND advisor_id = :advisor ORDER BY id");
                    $query->execute(array(
                        'search' => '%' . $_GET['search'] . '%',
                        'advisor' => $_COOKIE['advisor']
                    ));
                }
                $return = '';
                while ($data = $query->fetch()) {
                    echo '<tr><td>'. $data['surname'] .'</td><td>'. $data['lastname'] .'</td><td>'. $data['cohorte'] .'</td><td>'. $data['pay'] .' €</td><td>'. $data['update_date'] .'</td><td><a class="btn btn-primary" href="?type=profile&surname='. $data['surname'] .'&lastname='. $data['lastname'] .'">Voir fiche</a></td></tr>';
                }
                $query->closeCursor();
                echo '</tbody>
                </table>';
                break;
            case "profile":
                # When a profile is selected, show all activities
                $query = $db->prepare("SELECT activity_check.*, groups.lastname, groups.surname, groups.red_list, groups.active FROM activity_check LEFT JOIN groups ON activity_check.lastname = groups.lastname AND activity_check.surname = groups.surname WHERE activity_check.lastname = :lastname AND activity_check.surname = :surname ORDER BY activity_check.id DESC");
                $query->execute(array(
                    'lastname' => $_GET['lastname'],
                    'surname' => $_GET['surname']
                ));
                $data = $query->fetch();
                $query->closeCursor();
                ?>
                <div class="bg-light">
                    <div class="row py-4">
                        <div class="col-1 text-center">
                            <img src="./c_images/user.png" class="rounded-circle w-75">
                        </div>
                        <div class="col-7">
                            <strong>NOM</strong> : <?php echo $data['lastname']; ?><br>
                            <strong>Prénom</strong> : <?php echo $data['surname']; ?><br>
                            <strong>Groupe</strong> : <?php echo $data['cohorte']; ?>
                        </div>
                        <div class="col-3">
                            <strong>Actualisation</strong> : <?php echo $data['update_date']; ?><br>
                            <strong>Actualisé ?</strong> : <?php if ($data['update_date'] >= date('Y-m-01') || $data['update_date'] <= date('Y-m-31')) {echo '<span class="badge badge-success">OUI</span>';} ?><br>
                            <strong>Liste rouge ?</strong> : <?php if ($data['red_list'] == '1') {echo '<span class="badge badge-danger">OUI</span>';} else {echo '<span class="badge badge-secondary">NON</span>';} ?><br>
                            <strong>Dossier ouvert</strong> : <?php if ($data['active'] == 'false') {echo '<span class="badge badge-danger">NON</span>';} else {echo '<span class="badge badge-success">OUI</span>';} ?>
                        </div>
                    </div>
                    <table id="search" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="col-10">Heure</th>
                                <th class="col-2">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = $db->prepare("SELECT * FROM activity_check WHERE lastname = :lastname AND surname = :surname ORDER BY id");
                            $query->execute(array(
                                'lastname' => $_GET['lastname'],
                                'surname' => $_GET['surname']
                            ));
                            while($activity = $query->fetch()) {
                                echo '<tr>
                                    <td>'. $activity['update_date'] .'</td>
                                    <td><span class="badge badge-warning badge-rounded" style="font-size: 14px;">'. $activity['pay'] .'€</span></td>
                                </tr>';
                            }
                            $query->closeCursor();
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                break;
            default:
                # When no search is set
            ?>
            <div class="text-center my-4">
                <img src="./c_images/gj.png" title="Garantie Jeunes">
            </div>
            <form method="GET" action="search.php">
                <div class="input-group mb-3">
                    <input name="search" type="text" class="form-control" placeholder="Votre recherche" aria-label="Votre recherche" aria-describedby="button-search">
                    <input name="type" type="text" class="form-control hidden" value="search" aria-hidden="true" hidden>
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit" id="button-search"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
            <?php
            break;
            }
            ?>
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
<script src="./jscripts/jquery-3.4.1.min.js"></script>
<script src="./jscripts/sweetalert2.min.js"></script>
<script src="./jscripts/bootstrap.bundle.min.js"></script>
<script src="./jscripts/bootstrap.min.js"></script>
<script src="./jscripts/scripts.js"></script>
</body>
</html>