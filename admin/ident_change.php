<?php
session_start();
if (isset($_GET['id'])) {
	$_SESSION['advisor'] = $_GET['id'];
}
if (isset($_GET['adm'])) {
	$_SESSION['is_admin'] = true;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Administration - Garantie Jeunes</title>
	<!-- Metadata -->
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<!-- Styesheet -->
	<link href="https://use.fontawesome.com/releases/v5.10.2/css/all.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
</head>
<body>
	<?php if(isset($_GET['id'])) { ?>
	<div class="alert alert-success"><i class="fa fa-check-circle"></i> Vous êtes maitenant <strong><?php echo $_GET['id']; ?></strong></div>
	<?php } else { ?>
	<div class="alert alert-danger"><i class="fa fa-times-circle"></i> En attente d'un compte...</div>
	<?php } ?>
</body>
</html>