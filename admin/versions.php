<?php
require_once "./includes/config.php";
// Check session
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
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Support</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Support</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main content -->
		<div class="content">
			<div class="container-fluid">
				<div class="col">
					<div class="card">
						<div class="card-header border-0">
							<h3 class="card-title">Note de version</h3>
							<p class="card-text text-muted">Version complète : 1.1.2</p>
						</div>
						<div class="card-body">
							<h5 class="text-info"><i class="fad fa-bug"></i> Corrections</h5>
							<p class="card-text">
								• Un problème impactant le transfert de données et le reotur des informations a été corrigé.<br>
								• Un script mal excécuté sur des pages administratives a été corrigé.<br>
								• Un fichier appelé sur les pages a été déplacé pour éviter de potentiels problèmes.
							</p>
						</div>
					</div>
					<div class="card">
						<div class="card-header border-0">
							<h3 class="card-title">Note de version</h3>
							<p class="card-text text-muted">Version complète : 1.1.1</p>
						</div>
						<div class="card-body">
                            <h5 class="text-warning"><i class="fad fa-redo-alt"></i> Changements</h5>
							<p class="card-text">
								• Des valeurs ont été réajustées sur différentes pages :<br>
								 -> Le blocage liste rouge bloque le formulaire dure <span class="badge badge-danger">300s</span> > <span class="badge badge-success">60s</span><br>
								 -> Les notifications sur l'administration durent <span class="badge badge-danger">5s</span> > <span class="badge badge-success">300s</span>
							</p>
                            <hr>
							<h5 class="text-info"><i class="fad fa-bug"></i> Corrections</h5>
							<p class="card-text">
								• Un bug qui affectait les listes à choix en faisant des retours vers le haut a été corrigé.<br>
								• Un bug graphique ors de la désactivation des erreurs sur le formulaire a été corrigé.<br>
								• Un bug permettant d'enregistrer une déclaration quand la clé de formulaire n'était pas reconnue a été corrigé.<br>
								• Un bug qui empêchait les adminstrateurs de voir le nombre de notification a été corrigé.<br>
								• Le bouton de test des notifications a été changé sur le panneau des services civiques.<br>
								• L'éditeur de dossier jeune n'affichait pas le nom des conseillers mais leur identifiant.<br>
								• Un bug qui affectait les statistiques les rendant incorrectes a été corrigé.<br>
								• Un bug lors d'un retour arrière écrivait les textes du formulaire précédent a été corrigé<br>
								• Un bug permettant d'enregistrer un formulaire avec des lettres dans le montant a été corrigé.
							</p>
						</div>
					</div>
					<div class="card">
						<div class="card-header border-0">
							<h3 class="card-title">Note de version</h3>
							<p class="card-text text-muted">Version complète : 1.1.0</p>
						</div>
						<div class="card-body">
                            <h5 class="text-success"><i class="fad fa-plus"></i> Ajouts</h5>
							<p class="card-text">
								• Le gestionnaire des tâches débarque avec son lot de fonctionnalités :<br>
								&nbsp;-> Les mots de passes seront remis à 0 chaque mois.<br>
								&nbsp;-> Les déclarations anciennes de 1 an seront supprimées.<br>
								&nbsp;-> Les listes rouges seront remises à 0 chaque mois (sauf liste noire).<br>
								&nbsp;-> Les notification seront remises à 0 chaque mois.<br>
								&nbsp;-> Les liens externes seront périmés chaque mois.<br>
								• Nouveau système de reconnexion permettant de retaper le mot de passe pour rouvrir sa session.
							</p>
                            <hr>
                            <h5 class="text-warning"><i class="fad fa-redo-alt"></i> Changements</h5>
							<p class="card-text">
								• Le design du site "Formulaire" a été réduit pour rendre certaines parties plus lisibles.<br>
								• La page "Déclarations du mois" a été réduite en taille, l'affichage se fait maintenant en accordéon.
							</p>
                            <hr>
							<h5 class="text-info"><i class="fad fa-bug"></i> Corrections</h5>
							<p class="card-text">
								• Un problème de retour vers le haut de page a été corrigé sur le formulaire jeune.<br>
								• Un problème de statistique était présent sur les accueils des administrations.
							</p>
                            <hr>
                            <h5 class="text-dark"><i class="fad fa-server"></i> Serveur/Services</h5>
							<p class="card-text">
								• Une optimisation des fichiers a été effectuée avec une réduction des fichiers Scripts.<br>
								• Une redocumentation des fichiers a été effectuée.
                            </p>
						</div>
					</div>
					<div class="card">
						<div class="card-header border-0">
							<h3 class="card-title">Note de version</h3>
							<p class="card-text text-muted">Version complète : 1.0.1</p>
						</div>
						<div class="card-body">
                            <h5 class="text-success"><i class="fad fa-plus"></i> Ajouts</h5>
							<p class="card-text">
								• Plus puissant que la liste rouge, le blocage "liste noire" a été ajouté.<br>
								• Les cases vides du formulaire s'affichent maintenant en rouge.
							</p>
                            <hr>
                            <h5 class="text-warning"><i class="fad fa-redo-alt"></i> Changements</h5>
							<p class="card-text">
								• Le système de notification a été mis à jour.<br>
								• Le système de liste rouge a été enforcé, le jeune doit attendre 5 minutes avat de pouvoir rééditer sa déclaration.<br>
								• Les icônes sont à jour en DuoTones.<br>
								• Les dossiers fermés sont maintenant cachés dans la page "Déclarations du mois".<br>
								• L'administration des jeunes a eu une petite mise à jour de fonctionnalités.
							</p>
						</div>
					</div>
					<div class="card">
						<div class="card-header border-0">
							<h3 class="card-title">Note de version</h3>
							<p class="card-text text-muted">Version complète : 1.0.0</p>
						</div>
						<div class="card-body">
                            <h5 class="text-success"><i class="fad fa-plus"></i> Ajouts</h5>
							<p class="card-text">
								• La durée de génération des lien a été ajoutée.<br>
								• Le design des DataTable a été ajouté.
								• La page "Déclaration du mois" permet de consulter les déclarations.<br>
							</p>
                            <hr>
                            <h5 class="text-warning"><i class="fad fa-redo-alt"></i> Changements</h5>
							<p class="card-text">
								• Renforcement de la sécurité : il est plus difficile de notifier plusieurs fois un conseiller.<br>
								• Le panneau "Service civique" ne se somme plus "Administration".<br>
								• La page "Déclaration d'activité" a été allégée.
							</p>
                            <hr>
							<h5 class="text-info"><i class="fad fa-bug"></i> Corrections</h5>
							<p class="card-text">
								• La police de la page d'administration n'est pas la bonne.<br>
								• L'option "J'entre en Garantie Jeunes" bloquait la génération des PDFs.
							</p>
						</div>
					</div>
					<div class="card">
						<div class="card-header border-0">
							<h3 class="card-title">Note de version</h3>
							<p class="card-text text-muted">Version provisoire : 0.2.1</p>
						</div>
						<div class="card-body">
                            <h5 class="text-success"><i class="fad fa-plus"></i> Ajouts</h5>
							<p class="card-text">
								• Une page "Guide" a été ajoutée afin d'aider à comprendre tous les boutons.<br>
								• Un lien pour éditer les montant des déclarations "Intérim" a été ajouté.
                            </p>
                            <hr>
                            <h5 class="text-warning"><i class="fad fa-redo-alt"></i> Changements</h5>
							<p class="card-text">
								• Diverses corrections de fautes de frappe ont été faites.
							</p>
                            <hr>
                            <h5 class="text-info"><i class="fad fa-bug"></i> Corrections</h5>
							<p class="card-text">
								• Les statistiques ne s'initialisaient pas au mois actuel correctement.<br>
								• Les déclarations du mois n'affichaient plus les montants, et lorsqu'un jeu venait faire sa déclaration, sa ligne était doublée.
                            </p>
						</div>
					</div>
					<div class="card">
						<div class="card-header border-0">
							<h3 class="card-title">Note de version</h3>
							<p class="card-text text-muted">Version provisoire : 0.2.0</p>
						</div>
						<div class="card-body">
                            <h5 class="text-success"><i class="fad fa-plus"></i> Ajouts</h5>
							<p class="card-text">
                                • Les administrateurs peuvent voir toutes les listes de jeunes du mois.<br>
                                • Les conseillers peuvent créer des déclarations d'activité, elles s'affichent en Rouge dans la liste. <span class="text-danger"><i class="fa fa-exclamation"></i> ATTENTION : Ce bouton est fait pour les déclarations en retard, ne pas utiliser entre le 1<sup>er</sup> et le 15 !</span><br>
								• La provenance des déclaration d'activité a été ajoutée à la liste des déclarations d'activité.<br>
								• Une nouvelle page contenant la dernière note de mise à jour a été ajoutée afin de tenir informé des changements.
							</p>
                            <hr>
                            <h5 class="text-warning"><i class="fad fa-redo-alt"></i> Changements</h5>
							<p class="card-text">
                                • Le formulaire prend en compte si un jeune travaille en Intérim et n'a pas reçu son salaire.<br>
                                • Le formulaire a une nouvelle option "Entrée en Garantie Jeunes" pour les nouvelles Cohortes.
                            </p>
                            <hr>
                            <h5 class="text-info"><i class="fad fa-bug"></i> Corrections</h5>
							<p class="card-text">
                                • Une erreur du système de statistiques qui se déclenchait quand la session était perdue a été corrigée.
                            </p>
                            <hr>
                            <h5 class="text-dark"><i class="fad fa-server"></i> Serveur/Services</h5>
							<p class="card-text">
                                • Le serveur a été changé, toutes les données ont été récupérées.
                            </p>
                            <hr>
                            <h5 class="text-danger"><i class="fad fa-times"></i> Suppressions</h5>
							<p class="card-text">
                                • Le formulaire ne demandera plus la date du dernier rendez-vous.
                            </p>
						</div>
					</div>
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
<script src="./jscripts/jquery-3.4.1.min.js"></script>
<script src="./jscripts/sweetalert2.min.js"></script>
<script src="./jscripts/bootstrap.bundle.min.js"></script>
<script src="./jscripts/bootstrap.min.js"></script>
<script src="./jscripts/scripts.js"></script>
</body>
</html>