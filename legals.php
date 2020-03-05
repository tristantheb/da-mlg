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
    <div class="container card card-secondary card-outline">
        <div class="card-header">
            <h5 class="card-title">Mentions légales</h5>
        </div>
        <div class="card-body">
            <p class="card-text">
                <strong>Structure :</strong> Mission Locale des Graves<br>
                <strong>Adresse :</strong> Centre Ccial. La House, Chemin de la House, 33610 CANEJAN<br>
                <strong>Téléphone :</strong> 05.56.15.02.41
            </p>
        </div>
    </div>
    <div class="container card card-secondary card-outline">
        <div class="card-header">
            <h5 class="card-title">Traitement des données</h5>
        </div>
        <div class="card-body">
            <p class="card-text">
                <strong>Structure :</strong> Mission Locale des Graves (Garantie Jeunes)<br>
                <strong>Adresse :</strong> 94 Avenue de Canéjan, 33600 PESSAC
            </p>
            <hr>
            <p class="card-text">
                <strong>Finalités :</strong> Interne à la structure<br>
                <strong>Caractère obligatoire du recueil des données :</strong> Les données récoltées par le formulaire servent au suivi du dossier de chaque jeune inscrit à la Garantie Jeunes. Le refus de remplir le formulaire, ou le fait de ne pas le remplir correctement peut amener à des sanctions ou à une suspension de l'allocation perçue.<br>
                <strong>Destinataires :</strong> La Garantie Jeunes peut accéder aux données sous trois accès :<br>
                - Les Administrateurs ont un accès total aux données (Lecture, Modification, Accès total).<br>
                - Les Conseillers ont un accès aux données des dossiers qui leur sont confiés (Lecture, Modification, Accès limité).<br>
                - Les Services civiques ont un accès partiel aux données (Lecture, Accès limité).<br>
                <strong>Durée de conservation des données :</strong> Les données sont sauvegardées pour une durée de 1 an à compter de la date de création d'un document sur le site. La suppression de ces données est automatisée.<br>
                <strong>Droits des personnes concernées :</strong> Tous les jeunes peuvent demander la modification ou l'effacement des données, conséquences en suivent. En cas de demande d'effacement des données, aucune allocation ne pourra être déclenchée.
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
