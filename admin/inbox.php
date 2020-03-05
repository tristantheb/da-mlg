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
<link rel="stylesheet" type="text/css" href="style/summernote-bs4.css">
<body class="hold-transition sidebar-mini">
<div class="wrapper">
	<!-- Navigation -->
	<?php include_once "includes/tmpl_nav.php"; ?>
	<!-- Main Sidebar Container -->
	<?php include_once "includes/tmpl_sidebar.php"; ?>
    <!-- Contenu principal -->
    <?php if (!isset($_GET['view']) || $_GET['view'] === 'inbox') { ?>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Boîte de réception</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Boîte de réception</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- Main content -->
		<section class="content">
            <div class="row">
                <div class="col-md-3">
                    <a href="inbox.php?view=compose" class="btn btn-primary btn-block mb-3">Nouveau message</a>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Dossiers</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fad fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item active">
                                    <a href="inbox.php?view=inbox" class="nav-link">
                                        <i class="fad fa-inbox"></i> Boîte de réception
                                        <?php if(count_stats('da_mailbox', 'sent_to = '.$_COOKIE['advisor'].' AND read_date IS NULL') == 0) { ?>
                                        <span class="badge badge-secondary float-right">0</span>
                                        <?php } else { ?>
                                        <span class="badge bg-primary float-right"><?php echo count_stats('da_mailbox', 'sent_to = '.$_COOKIE['advisor'].' AND read_date IS NULL'); ?></span>
                                        <?php } ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="inbox.php?view=sent" class="nav-link">
                                        <i class="fad fa-envelope"></i> Envoyés
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="inbox.php?view=trash" class="nav-link">
                                        <i class="fad fa-trash-alt"></i> Corbeille
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <div class="col-md-9">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h4 class="card-title">Boîte de réception</h4>
                        </div>
                        <table id="inbox" class="table table-striped card-body p-0">
                            <thead>
                                <tr>
                                    <th class="col-md-3">De</th>
                                    <th class="col-md-6">Titre</th>
                                    <th class="col-md-3">Reçu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = $db->prepare('SELECT * FROM da_mailbox WHERE sent_to = :id ORDER BY send_date DESC');
                                $query->execute(array(
                                    'id' => $_COOKIE['advisor']
                                ));
                                while ($data = $query->fetch()) {
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        $query_name = $db->prepare("SELECT advisor_id, full_name FROM advisors WHERE advisor_id = :sent_by");
                                        $query_name->execute(array(
                                            'sent_by' => $data['sent_by']
                                        ));
                                        $name = $query_name->fetch();
                                        echo $name['full_name'];
                                        $query_name->closeCursor();
                                        ?>
                                    </td>
                                    <td><a href="inbox.php?view=read&id=<?php echo $data['id']; ?>"><?php echo $data['title']; ?></a></td>
                                    <td><?php echo $data['send_date']; ?></td>
                                </tr>
                                <?php }
                                $query->closeCursor();
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>De</th>
                                    <th>Titre</th>
                                    <th>Reçu</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
		</section>
	</div> 
    <?php } elseif ($_GET['view'] == 'read' && isset($_GET['id'])) {
        // On read, update date
        $date_up = date('Y-m-d H:i:s');
        $update = $db->prepare("UPDATE da_mailbox SET read_date=NOW() WHERE id = :id");
        $update->execute(array(
            'id' => $_GET['id']
        ));
        $update->closeCursor();
        // Select content
        $query = $db->prepare('SELECT * FROM da_mailbox WHERE sent_to = :id AND id = :dm_id ORDER BY send_date DESC');
        $query->execute(array(
            'id' => $_COOKIE['advisor'],
            'dm_id' => $_GET['id']
        ));
        $data = $query->fetch();
    ?>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Lecture de message</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item"><a href="inbox.php?view=inbox">Boîte de réception</a></li>
							<li class="breadcrumb-item active">Lecture de message</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- Main content -->
		<section class="content">
            <div class="row">
                <div class="col-md-3">
                    <a href="inbox.php?view=inbox" class="btn btn-primary btn-block mb-3">Revenir à la Réception</a>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Dossiers</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fad fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item active">
                                    <a href="inbox.php?view=inbox" class="nav-link">
                                        <i class="fad fa-inbox"></i> Boîte de réception
                                        <?php if(count_stats('da_mailbox', 'sent_to = '.$_COOKIE['advisor'].' AND read_date IS NULL') == 0) { ?>
                                        <span class="badge badge-secondary float-right">0</span>
                                        <?php } else { ?>
                                        <span class="badge bg-primary float-right"><?php echo count_stats('da_mailbox', 'sent_to = '.$_COOKIE['advisor'].' AND read_date IS NULL'); ?></span>
                                        <?php } ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="inbox.php?view=sent" class="nav-link">
                                        <i class="fad fa-envelope"></i> Envoyés
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="inbox.php?view=trash" class="nav-link">
                                        <i class="fad fa-trash-alt"></i> Corbeille
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
                    switch ($data['type']) {
                        case 'information':
                            $type = "card-info";
                            $badge = '<span class="badge badge-info float-right">Information</span>';
                            break;
                        case 'important':
                            $type = "card-warning";
                            $badge = '<span class="badge badge-warning float-right">Important</span>';
                            break;
                        case 'urgent':
                            $type = "card-danger";
                            $badge = '<span class="badge badge-danger float-right">Urgent</span>';
                            break;
                    }
                    ?>
                    <div class="card card-outline <?php echo $type; ?>">
                        <div class="card-header">
                            <h4 class="card-title"><?php echo $data['title']; echo $badge; ?></h4>
                        </div>
                        <div class="card-body">
                            Reçu <?php echo count_duration($data['send_date']); ?> - Dernière lecture <?php echo count_duration($data['read_date']); ?>
                            <hr>
                            <?php echo $data['content']; ?>
                        </div>
                        <div class="card-footer">
                            <span class="text-muted">
                                De <?php $query_name = $db->prepare("SELECT advisor_id, full_name FROM advisors WHERE advisor_id = :sent_by");
                                $query_name->execute(array(
                                    'sent_by' => $data['sent_by']
                                ));
                                $name = $query_name->fetch();
                                echo $name['full_name'];
                                $query_name->closeCursor(); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
		</section>
	</div>
    <?php
    $query->closeCursor();
    } elseif ($_GET['view'] == 'sent') {
    ?>
    <div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Messages envoyés</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Messages envoyés</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- Main content -->
		<section class="content">
            <div class="row">
                <div class="col-md-3">
                    <a href="inbox.php?view=inbox" class="btn btn-primary btn-block mb-3">Revenir à la Réception</a>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Dossiers</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fad fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item active">
                                    <a href="inbox.php?view=inbox" class="nav-link">
                                        <i class="fad fa-inbox"></i> Boîte de réception
                                        <?php if(count_stats('da_mailbox', 'sent_to = '.$_COOKIE['advisor'].' AND read_date IS NULL') == 0) { ?>
                                        <span class="badge badge-secondary float-right">0</span>
                                        <?php } else { ?>
                                        <span class="badge bg-primary float-right"><?php echo count_stats('da_mailbox', 'sent_to = '.$_COOKIE['advisor'].' AND read_date IS NULL'); ?></span>
                                        <?php } ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="inbox.php?view=sent" class="nav-link">
                                        <i class="fad fa-envelope"></i> Envoyés
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="inbox.php?view=trash" class="nav-link">
                                        <i class="fad fa-trash-alt"></i> Corbeille
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <div class="col-md-9">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h4 class="card-title">Messages envoyés</h4>
                        </div>
                        <table id="inbox" class="table table-striped card-body p-0">
                            <thead>
                                <tr>
                                    <th class="col-md-3">À</th>
                                    <th class="col-md-6">Titre</th>
                                    <th class="col-md-3">Lu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = $db->prepare('SELECT * FROM da_mailbox WHERE sent_by = :id ORDER BY send_date DESC');
                                $query->execute(array(
                                    'id' => $_COOKIE['advisor']
                                ));
                                while ($data = $query->fetch()) {
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        $query_name = $db->prepare("SELECT advisor_id, full_name FROM advisors WHERE advisor_id = :sent_to");
                                        $query_name->execute(array(
                                            'sent_to' => $data['sent_to']
                                        ));
                                        $name = $query_name->fetch();
                                        echo $name['full_name'];
                                        $query_name->closeCursor();
                                        ?>
                                    </td>
                                    <td><?php echo $data['title']; ?></td>
                                    <td><?php if ($data['read_date'] == NULL) {echo 'Non lu';} else {echo $data['read_date'];} ?></td>
                                </tr>
                                <?php }
                                $query->closeCursor();
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>À</th>
                                    <th>Titre</th>
                                    <th>Lu</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
    } elseif ($_GET['view'] == 'trash') {
    ?>
    <div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Corbeille</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Corbeille</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- Main content -->
		<section class="content">
            <div class="row">
                <div class="col-md-3">
                    <a href="inbox.php?view=inbox" class="btn btn-primary btn-block mb-3">Revenir à la Réception</a>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Dossiers</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fad fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item active">
                                    <a href="inbox.php?view=inbox" class="nav-link">
                                        <i class="fad fa-inbox"></i> Boîte de réception
                                        <?php if(count_stats('da_mailbox', 'sent_to = '.$_COOKIE['advisor'].' AND read_date IS NULL') == 0) { ?>
                                        <span class="badge badge-secondary float-right">0</span>
                                        <?php } else { ?>
                                        <span class="badge bg-primary float-right"><?php echo count_stats('da_mailbox', 'sent_to = '.$_COOKIE['advisor'].' AND read_date IS NULL'); ?></span>
                                        <?php } ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="inbox.php?view=sent" class="nav-link">
                                        <i class="fad fa-envelope"></i> Envoyés
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="inbox.php?view=trash" class="nav-link">
                                        <i class="fad fa-trash-alt"></i> Corbeille
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <div class="col-md-9">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h4 class="card-title">Messages supprimés</h4>
                        </div>
                        <table id="inbox" class="table table-striped card-body p-0">
                            <thead>
                                <tr>
                                    <th class="col-md-3">À</th>
                                    <th class="col-md-6">Titre</th>
                                    <th class="col-md-3">Lu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = $db->prepare('SELECT * FROM da_mailbox WHERE sent_by = :id AND trash = :trash ORDER BY send_date DESC');
                                $query->execute(array(
                                    'id' => $_COOKIE['advisor'],
                                    'trash' => 'true'
                                ));
                                while ($data = $query->fetch()) {
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        $query_name = $db->prepare("SELECT advisor_id, full_name FROM advisors WHERE advisor_id = :sent_to");
                                        $query_name->execute(array(
                                            'sent_to' => $data['sent_to']
                                        ));
                                        $name = $query_name->fetch();
                                        echo $name['full_name'];
                                        $query_name->closeCursor();
                                        ?>
                                    </td>
                                    <td><?php echo $data['title']; ?></td>
                                    <td><?php if ($data['read_date'] == NULL) {echo 'Non lu';} else {echo $data['read_date'];} ?></td>
                                </tr>
                                <?php }
                                $query->closeCursor();
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>À</th>
                                    <th>Titre</th>
                                    <th>Lu</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
    } elseif ($_GET['view'] == 'compose') {
        if (isset($_POST['submit'])) {
            $query_dm = $db->prepare("INSERT INTO da_mailbox (`sent_by`, `sent_to`, `title`, `content`, `type`, `send_date`) VALUES (:sent_by, :sent_to, :title, :content, :type_post, NOW())");
            $query_dm->execute(array(
                'sent_by' => $_COOKIE['advisor'],
                'sent_to' => $_POST['send_to'],
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'type_post' => $_POST['type']
            ));
            $query_dm->closeCursor();
        }
    ?>
    <div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Rédiger un message</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item"><a href="inbox.php?view=inbox">Boîte de réception</a></li>
							<li class="breadcrumb-item active">Rédiger un message</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- Main content -->
		<section class="content">
            <div class="row">
                <div class="col-md-3">
                    <a href="inbox.php?view=inbox" class="btn btn-primary btn-block mb-3">Revenir à la Réception</a>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Dossiers</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fad fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item active">
                                    <a href="inbox.php?view=inbox" class="nav-link">
                                        <i class="fad fa-inbox"></i> Boîte de réception
                                        <?php if(count_stats('da_mailbox', 'sent_to = '.$_COOKIE['advisor'].' AND read_date IS NULL') == 0) { ?>
                                        <span class="badge badge-secondary float-right">0</span>
                                        <?php } else { ?>
                                        <span class="badge bg-primary float-right"><?php echo count_stats('da_mailbox', 'sent_to = '.$_COOKIE['advisor'].' AND read_date IS NULL'); ?></span>
                                        <?php } ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="inbox.php?view=sent" class="nav-link">
                                        <i class="fad fa-envelope"></i> Envoyés
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="inbox.php?view=trash" class="nav-link">
                                        <i class="fad fa-trash-alt"></i> Corbeille
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <div class="col-md-9">
                    <form method="POST" action="" class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Nouveau message</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-group">
                                <select class="form-control" name="send_to">
                                    <option>Sélectionner un destinataire</option>
                                    <?php
                                    $query = $db->prepare("SELECT * FROM advisors WHERE active = 'true'");
                                    $query->execute();
                                    while ($advisors = $query->fetch()) {
                                        echo '<option value="'.$advisors['advisor_id'].'">'.$advisors['full_name'].'</option>';
                                    }
                                    $query->closeCursor();
                                    ?>
                                </select>
                            </div>
                            <div class="form-group row">
                                <div class="input-group col-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-info border-info"><input id="type" name="type" type="radio" value="information"></span>
                                    </div>
                                    <input type="text" class="form-control bg-info border-info" value="Information" disabled>
                                </div>
                                <div class="input-group col-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-warning border-warning"><input id="type" name="type" type="radio" value="important"></span>
                                    </div>
                                    <input type="text" class="form-control bg-warning border-warning" value="Important" disabled>
                                </div>
                                <div class="input-group col-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-danger border-danger"><input id="type" name="type" type="radio" value="urgent"></span>
                                    </div>
                                    <input type="text" class="form-control bg-danger border-danger" value="Urgent" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="title" placeholder="Sujet">
                            </div>
                            <div class="form-group">
                                <textarea id="formeditor" class="form-control" name="content" style="height: 300px"></textarea>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="float-right">
                                <button type="submit" name="submit" class="btn btn-primary"><i class="fad fa-envelope"></i> Envoyer</button>
                            </div>
                            <button type="reset" class="btn btn-default"><i class="fad fa-times"></i> Abandonner</button>
                        </div>
                        <!-- /.card-footer -->
                    </form>
                </div>
            </div>
		</section>
    </div>
    <?php } ?>
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
<!-- DataTables -->
<script src="./jscripts/jquery.dataTables.min.js"></script>
<script src="./jscripts/dataTables.bootstrap4.min.js"></script>
<!-- Summernote -->
<script src="./jscripts/summernote-bs4.min.js"></script>
<!-- Scripts -->
<script src="./jscripts/scripts.js"></script>
<script>
$(function () {
    $("#inbox").DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": true,
        "autoWidth": false
    });
    $("#formeditor").summernote({
        fontNames: ['Arial'],
        placeholder: 'Écrivez votre message ici',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    });
});
</script>
</body>
</html>