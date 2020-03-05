<?php
require "includes/config.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Déclaration d'activité - Garantie Jeunes</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta content="<?php echo VERSION; ?>" name="version">
	<!-- Stylesheet -->
	<link rel="stylesheet" type="text/css" href="./style/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./style/style.css">
	<link rel="stylesheet" type="text/css" href="./style/fontawesome.min.css">
</head>
<body>
	<header class="da-header bg-white shadow-sm mb-3">
		<div class="row">
			<div class="col-12 col-sm-3 col-md-2 col-lg-1 text-center">
				<img class="w-75 m-1" src="./c_images/garantie-jeunes.png" alt="Garantie Jeunes">
			</div>
			<div class="col-12 col-sm-9 col-md-10 col-lg-11">
				<h1 class="my-2">Déclaration d'activité</h1>
			</div>
		</div>
	</header>
	<div class="container">
		<div class="callout callout-danger">
			<h5>403 Forbidden acces to this page</h5>
			<p>
				La page que vous avez essayé d'appeler n'est pas accessible pour l'une des raisons suivantes :<br><br>
				- La clé d'accès n'existe pas<br>
				- La clé d'accès est périmée<br><br>
				Vérifiez que vous utilisez une clé d'accès valide et retentez d'accéder au lien, si le problème persiste, contactez votre conseiller pour obtenir une nouvelle clé.
			</p>
		</div>
	</div>
	<footer class="container card">
		<div class="card-body">
			<div class="d-inline-block w-100">
				<img src="c_images/logo-mlg.png" alt="Mission Locale des Graves">
				<img src="c_images/ministere_travail.jpg" alt="Ministère du Travail">
				<img src="c_images/logoFSE.jpg" alt="Union Européenne et FSE">
			</div>
			<p>
				<strong>Garantie Jeunes © 2019</strong><br>
				Ce site a été créé et codé pour la Mission Locale des Graves dans le cadre de la Garantie Jeunes.<br><br>
				Version <?php echo VERSION; ?>
			</p>
			<a href="legals.php" class="card-link">Mentions légales</a>
		</div>
	</footer>
</body>
</html>
