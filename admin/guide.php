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
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Guide</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
							<li class="breadcrumb-item active">Guide</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- Main content -->
		<div class="content">
			<div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Les menus</h5>
                    </div>
                    <div class="card-body">
                        Sur la partie gauche vous trouverez les menus suivants :<br>
                        <ul>
                            <li>Tableau de bord <span class="text-muted">Accueil du site avec les statistiques</span></li>
                            <li>
                                Les déclarations
                                <ul>
                                    <li>Déclarations d'activité <span class="text-muted">Permet de voir toutes les déclarations d'activité et de les imprimer.</span></li>
                                    <li>Déclaration ce-mois <span class="text-muted">Permet de voir les listes des groupes pour savoir qui n'a pas fait sa déclaration durant le mois en cours.</span></li>
                                    <li>Générer un lien <span class="text-muted">Permet de générer un lien à usage unique qui peut ensuite être envoyé aux jeunes.</span></li>
                                    <li>Faire une DA admin <span class="text-muted">Lien à utiliser entre le 16 et le 31 du mois avec un courrier adressé à Hakim.</span></li>
                                    <li>Faire une DA jeune <span class="text-muted">Lien à utiliser entre le 1<sup>er</sup> et le 15 du mois pour faire une déclaration.</span></li>
                                </ul>
                            </li>
                            <li>
                                La zone rouge
                                <ul>
                                    <li>Liste rouge <span class="text-muted">Permet d'ajouter ou retirer un jeune en liste rouge.</span></li>
                                    <li>Notifications <span class="text-muted">Permet de voir les jeunes étant passés sur le site alors qu'ils sont en liste rouge.</span></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card card-outline card-red">
                    <div class="card-header">
                        <h5 class="card-title">Les déclarations</h5>
                    </div>
                    <div class="card-body">
                        <h5>Déclarations d'activité</h5>
                        Sous la forme d'un tableau, vous pouvez retrouver toutes les déclarations faites par les jeunes et les imprimer.<br>
                        Ce même tableau vous permet de savoir d'où provient la déclaration d'activité :<br>
                        - <span class="badge badge-primary">Interne</span> : Fait à la Garantie Jeunes ;<br>
                        - <span class="badge badge-success">Externe</span> : Fait avec un lien à usage unique pour les jeunes ;<br>
                        - <span class="badge badge-danger">Conseiller</span> : Fait à partir du lien spécial (admin) par les conseillers.<br><br>
                        Les déclarations affichent un récapitulatif de ce qui a (ou non) été coché/rempli par le jeune.<br>
                        Dans le cas où le récapitulatif contient un "no €", un lien pour éditer le montant est disponible<br><br>
                        L'impression depuis ce panneau effectue une signature numérique. Si le jeune est présent, il peut faire une signature par dessus pour attester.<br>
                        Pour pouvoir imprimer, il vous suffit de cliquer sur le bouton <button>Générer un PDF</button> se trouvant en bas.
                        <hr>
                        <h5>Déclarations ce-mois</h5>
                        Sur cette page, la gestion est faite de telle sorte que vous ne puissiez voir, uniquement vos jeunes, rangés par groupes (1 tableau = 1 groupe). Sur ce format s'affiche une case pour vérifier quand un jeune a fait ou non sa déclaration.<br><br>
                        Pour bien comprendre l'affichage, voici comment lire :<br>
                        - <span class="badge badge-pill badge-dark">Dossier fermé</span> : Le jeune a [été sortit du/a terminé le] dispositif ;<br>
                        - <span class="badge badge-pill badge-danger">Pas de déclaration</span> : Le jeune n'est pas venu faire sa déclaration ;<br>
                        - <span class="badge badge-pill badge-warning">&lt;un montant&gt;€</span> : Le jeune a fait sa déclaration ;<br>
                        - <span class="text-danger">Montant manquant</span> : Le jeune s'est déclaré en Intérim, il a donc un montant temporaire que devra éditer son conseiller.
                        <h5>Générer un lien</h5>
                        Cette page vous permet de créer un lien à usage unique qui est à envoyer aux jeunes ne pouvant pas se déplacer pour des raisons médicales, d'emplois, ou autres raisons justifiées.<br>
                        Lorsque la déclaration d'activité du jeune se valide, le lien devient "périmé", ce qui empêche ce jeune de pouvoir réutiliser le lien ou même de le donner à quelqu'un d'autre. La mise en mode "périmé" du lien le rend définitivement obsolète, il ne peut plus jamais être généré, utilisé, donné.<br><br>
                        Les liens que vous générez se classent dans un tableau vous permettant de voir les codes utilisés ou non. Si un code n'est pas utilisé à la fin du mois, réutilisez-le pour un autre jeune afin de le rendre périmé lors du mois suivant. Les liens utilisés sont automatiquement effacés de votre liste.<br><br>
                        La durée d'accès à ce lien est indiquée dans la partie "Site jeunes"
                        <hr>
                        <h5>Faire une DA jeune</h5>
                        Du 1<sup>er</sup> au 15 du mois, c'est ce lien qu'il faut utiliser. Il vous permet de générer avec le jeune, une déclaration d'activité en interne.
                        <hr>
                        <h5>Faire une DA admin</h5>
                        Du 16 au 31 du mois, c'est ce lien qu'il faut utiliser. <strong>Pour rappel</strong>, ils doivent, dans un premier temps, écrire un courrier au Coordinateur de la Garantie Jeunes et attendre sa validation. La validation obtenue, ils passeront par ce lien pour faire leur déclaration.
                    </div>
                </div>
                <div class="card card-outline card-red">
                    <div class="card-header">
                        <h5 class="card-title">La zone rouge</h5>
                    </div>
                    <div class="card-body">
                        <h5>Liste rouge</h5>
                        Le tableau de cette page vous permet de voir quels sont les jeunes que vous voulez bloquer. Tous les jeunes sur cette liste ne pourront pas faire leur déclaration d'activité tant que <u>le conseiller référent</u> ne les débloque pas.<br>
                        Dès qu'un jeune dans cette liste se présente, il déclenche une alerte au moment de valider sa déclaration d'activité de votre côté. De son côté, il se retrouve bloqué par le système.
                        <hr>
                        <h5>Notifications</h5>
                        Sur cette page, tous les jeunes ayant déclenchés une alerte se retrouvent listés avec leur heure de passage. La notification est considérée comme "Nouvelle" durant 1 heure seulement. Après, le jeune est considéré comme "parti", et la notification passe en mode "Ancienne".
                    </div>
                </div>
                <div class="card card-outline card-blue">
                    <div class="card-header">
                        <h5 class="card-title">Site jeunes</h5>
                    </div>
                    <div class="card-body">
                        Le site jeune est dôté d'un formulaire en 4 étapes. Ce formulaire est accessible entre le 1<sup>er</sup> et le 15 du mois en cours. Un script arrête l'accès à cette page dès le 16 à 0h00 (C'est à ce moment que la page admin entre en jeu et qu'il faut transmettre une lettre d'explication).<br>
                        <strong>EXCEPTION :</strong> Les entrées de groupe n'ont pas besoin d'une lettre et peuvent se faire depuis la page admin.<br><br>
                        <h4 class="text-blue">Étape 1</h4>
                        Ce formulaire, rappelle les règles actuellement en vigueur.<br><br>
                        <h4 class="text-blue">Étape 2</h4>
                        On commence à demander au jeune ses informations personnelles et son activité au cours du mois précédent. Il doit entrer le Mois, les deux derniers chiffres de l'Année, choisir son conseiller et remplir les activités.<br>
                        Si le jeune écrit bien son prénom/nom, le groupe se complète automatiquement. Sinon, il fait une erreur, et la bonne orthographe peut être vérifiée depuis la liste "Ce mois" à tout moment.<br><br>
                        <h5>Les activités :</h5>
                        <strong>1. Travail :</strong> Le jeune donne les entreprises et les types de contrat. Il devra avoir connaissance de son salaire (total), le cas échéant il n'a pas le droit de compléter sa déclaration.<br>
                        <strong>2.1. Atelier/Événement :</strong> Le jeune doit donner les ateliers et/ou événements auquels il a participé.<br>
                        <strong>2.2. Santé/Mobilité/etc... :</strong> Le jeune décrit les démarches spéciales qu'il a effectué afin d'améliorer son train de vie.<br>
                        <strong>2.3. Candidatures :</strong> Le jeune liste toutes les entreprises ayant reçu sa candidature pour des stages ou de l'emploi.<br>
                        <strong>3. Formation :</strong> Le jeune déclare avoir été en formation, il donne sa formation et le titre obtenu (si titre présent).<br>
                        <strong>4.1. Autres démarches :</strong> Le jeune déclare les autres actions qu'il a fait, comme un stages par exemple.<br>
                        <strong>4.2. Rien fait :</strong> Le jeune explique pourquoi il n'a rien fait.<br>
                        <strong>5. Entrée en Garantie Jeunes :</strong> Réservé aux jeunes qui entrent en Garantie Jeunes (Première déclaration).<br><br>
                        <h4 class="text-blue">Étape 3</h4>
                        Le jeune doit déclarer le montant perçu (hors allocation GJ ou RSA). <u>À noter :</u> S'il perçoit le RSA, il met 0 et coche la case RSA, la déclaration sera à saisir pour qu'il n'obtienne pas l'allocation, le RSA ne permettant pas de cumuler l'allocation.<br><br>
                        Selon son montant, il coche ensuite les cases nécessaires au remplissage du formulaire. S'il est à 0 et ne perçoit aucune aide, il ne coche rien.<br>
                        Il doit certifier son document.<br><br>
                        <h4 class="text-blue">Étape 4</h4>
                        Deux possibilités peuvent s'observer ici :<br>
                        1. Le jeune est en liste rouge, il ne peut pas accéder à cette étape sans avoir vu son conseiller. Seul le conseiller peut débloquer la suite.<br>
                        2. Le jeune peut générer et imprimer son PDF pour le signer.
                    </div>
                </div>
            </div>
		</div>
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