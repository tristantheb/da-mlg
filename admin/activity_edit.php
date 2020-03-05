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
// Check id
if(!isset($_GET['id'])) {
    header('Location: activity_check.php');
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

// On submit form
if (isset($_POST['submit'])) {
    $query_update = $db->prepare('UPDATE activity_check SET pay=:pay, chk_interim="false", chk_salaire="true" WHERE id = :id');
    $query_update->execute(array(
        'id' => $_GET['id'],
        'pay' => $_POST['pay']
    ));
    $query_update->closeCursor();
    header('Location: activity_check.php?show='.$_GET['id'].'');
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
                            <h1 class="m-0 text-dark">Éditer une déclaration d'activité</h1>
                            <p class="m-0 text-muted">Vous êtes sur une déclaration dont le montant n'a pas été déclaré. Vous pouvez ici, éditer le montant.</p>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
                                <li class="breadcrumb-item"><a href="activity_check.php?show=<?php echo $_GET['id']; ?>">Déclarations d'activité</a></li>
                                <li class="breadcrumb-item active">Éditer</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Éditer une déclaration d'activité</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <?php
                        $query = $db->prepare('SELECT * FROM activity_check WHERE id = :id');
                        $query->execute(array(
                            'id' => $_GET['id']
                        ));
                        $data = $query->fetch();
                        ?>
                        <h4>Rappel des informations primaires</h4>
                        <p>
                            <strong>Prénom : <?php echo $data['surname']; ?></strong><br>
                            <strong>Nom : <?php echo $data['lastname']; ?></strong><br>
                            <strong>Groupe : <?php echo $data['cohorte']; ?></strong><br><br>
                            Déclare avoir travaillé en Intérim sans avoir perçu de salaire au <strong><?php echo $data['update_date']; ?></strong><br><br>
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="pay">Nouveau montant :</label>
                                    <input id="pay" name="pay" class="form-control" type="text" placeholder="Montant" required autocomplete="off">
                                </div>
                                <input name="submit" type="submit" class="btn btn-outline-primary" value="Valider">
                            </form>
                        </p>
                        <?php
                        $query->closeCursor();
                        ?>
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
    <script defer src="./jscripts/scripts.js"></script>
</body>
</html>