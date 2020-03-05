<?php
require_once "./includes/config.php";
if (!isLogged() && !isIdentified()) {
	header('Location: ./login.php');
} elseif (!isLogged() && isIdentified()) {
	header('Location: ./lockscreen.php');
}
// Create date vars to auto complete the form
$act_date = strtotime(date("Y-m"));
$form_date = date("Y-m", strtotime("-1 month", $act_date));
$y_date = date("y", strtotime($form_date));
$m_date = date("F", strtotime($form_date));
switch ($m_date) {
	case 'January':
		$m_date = 'Janvier';
		break;
	case 'February':
		$m_date = 'Février';
		break;
	case 'March':
		$m_date = 'Mars';
		break;
	case 'April':
		$m_date = 'Avril';
		break;
	case 'May':
		$m_date = 'Mai';
		break;
	case 'June':
		$m_date = 'Juin';
		break;
	case 'July':
		$m_date = 'Juillet';
		break;
	case 'August':
		$m_date = 'Août';
		break;
	case 'September':
		$m_date = 'Septembre';
		break;
	case 'October':
		$m_date = 'Octobre';
		break;
	case 'November':
		$m_date = 'Novembre';
		break;
	case 'December':
		$m_date = 'Décembre';
		break;
}
// Define key type for database
$key_type = '<p class="m-0">Lien Administrateur</p><p class="m-0">Accès spécial</p>';
$link_type = 'advisor';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Déclaration d'activité - Garantie Jeunes</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta content="<?php echo VERSION; ?>" name="version">
	<!-- Stylesheet -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700">
	<link rel="stylesheet" type="text/css" href="./style/fontawesome.min.css">
	<link rel="stylesheet" type="text/css" href="./style/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./style/style_da.css">
	<link rel="stylesheet" type="text/css" href="./style/sweetalert2.min.css">
	<link rel="stylesheet" type="text/css" href="./style/toastr.min.css">
</head>
<body>
	<header class="da-header bg-white shadow-sm">
		<div class="row">
			<div class="col-12 col-sm-3 col-md-2 col-lg-1 text-center">
				<img class="w-75 m-1" src="c_images/garantie-jeunes.png" alt="Garantie Jeunes">
			</div>
			<div class="col-12 col-sm-6 col-lg-9">
				<h1 class="my-2">Déclaration d'activité</h1>
			</div>
			<div class="col-12 col-sm-3 col-md-2 text-center">
				<span class="my-2 badge badge-pill badge-danger"><?php echo $key_type; ?></span>
			</div>
		</div>
	</header>
	<?php
	$query = $db->prepare('SELECT * FROM gj_alerts WHERE active = "true"');
	$query->execute();
	while ($data = $query->fetch()) {
		switch ($data['type']) {
			case "info":
			$icon = "info-circle";
			break;
			case "success":
			$icon = "check";
			break;
			case "warning":
			$icon = "exclamation-triangle";
			break;
			case "danger":
			$icon = "exclamation-circle";
			break;
			case "dark":
			$icon = "question-circle";
			break;
		}
	?>
		<div class="d-block py-1 bg-<?php echo $data['type']; ?>">
			<div class="container">
				<h4 class="mb-0"><i class="fa fa-<?php echo $icon; ?>"></i> Information :</h4>
				<p class="mb-0"><?php echo $data['content']; ?></p>
			</div>
		</div>
	<?php
	}
	$query->closeCursor();
	?>
	<div id="error">&nbsp;</div>
	<div id="step1" class="container card card-primary card-outline">
		<div class="card-header">
			<h3 class="card-title">Étape 1 - Introduction et Règlement</h3>
		</div>
		<div class="card-body">
			<h4>Informations importantes</h4>
			<div class="alert alert-danger"><strong><i class="fad fa-exclamation-triangle"></i> Les déclarations qui passent par ce panneau doivent avoir été validées par le Coordinateur de la Garantie Jeunes.</strong></div>
			<p>
				Prenez bien le temps de tout lire avant de compléter. Les informations, pour chaque contenu, sont indiquées.<br>
				Il ne sert à rien de remplir rapidement le formulaire, si c'est pour mal le remplir.<br>
				Vous devez indiquer toutes les informations demandées pour toutes les cases obligatoires ou cases cochées contenant une zone de texte remplissable (cf. étape 2), ces informations sont à compléter correctement, <span class="text-danger">des sanctions peuvent être distribuées si vous remplissez mal les cases</span>.
			</p>
			<div class="border border-info border-rounded p-2">
				<h4 class="t-blue">Rappel des règles en vigueur</h4>
				<p class="m-0">
					<i class="fad fa-calendar-alt"></i> La déclaration doit se faire entre le <strong>1er et le 15 de chaque mois</strong>.<br>
					<i class="fad fa-gavel"></i> Toute fausse déclaration peut entrainer une suspension de l'Allocation GJ.<br>
					<span class="text-danger"><i class="fad fa-exclamation-triangle"></i> Les papiers justificatifs doivent être joints à la déclaration (Contrat de travail, Bulletin de paye, Allocation Pôle Emploi).</span><br>
					<i class="fad fa-file-signature"></i> Vous devez signer la version papier de la déclaration une fois le formulaire validé.
				</p>
			</div>
			<button id="certifRules" class="btn btn-primary float-right" onclick="nextStep();return false;">Suivant <i class="fad fa-arrow-right"></i></button>
		</div>
	</div>
	<div id="step2" class="container-fluid col-12 col-md-11 col-xl-10 card card-primary card-outline">
		<div class="card-header">
			<h3 class="card-title">Étape 2 - Formulaire de Déclaration d'Activité de la Garantie Jeunes</h3>
		</div>
		<div class="card-body">
			<h4>Mes informations principales pour le mois de <input id="month" class="form-control d-inline" type="text" placeholder="Mois" required autocomplete="off" style="font-size:.8em;width:125px" value="<?php echo $m_date; ?>" disabled> 20<input id="year" class="form-control d-inline" type="text" placeholder="Année" required autocomplete="off" maxlength="2" style="font-size:.8em;width:75px" value="<?php echo $y_date; ?>" disabled></h4>
			<div class="row">
				<div class="form-group col-12 col-sm-6">
					<label for="lastname">Nom :</label>
					<input id="lastname" class="form-control" type="text" placeholder="NOM" required autocomplete="off" onkeyup="this.value = this.value.toUpperCase();">
				</div>
				<div class="form-group col-12 col-sm-6">
					<label for="surname">Prénom :</label>
					<input id="surname" class="form-control" type="text" placeholder="Prénom" required autocomplete="off" onkeyup="this.value = this.value.charAt(0).toUpperCase() + value.substr(1);">
				</div>
			</div>
			<div class="form-group">
				<label for="group">Groupe GJ :</label>
				<input id="group" class="form-control" type="text" placeholder="Groupe" readonly required autocomplete="off" onselect="complete_group()" onmousedown="complete_group()">
			</div>
			<br>
			<div class="form-group">
				<label for="advisor">Conseiller(ère) :</label>
				<select id="advisor" class="custom-select form-control" type="text" required>
				<option selected>Sélectionner un conseiller</option>
				<?php
				$query = $db->prepare("SELECT * FROM advisors WHERE adm_lvl = '2' AND active = 'true'");
				$query->execute();
				while ($data = $query->fetch()) {
					echo '<option id="val' . $data['advisor_id'] . '" value="' . $data['advisor_id'] . '">' . $data['surname'] . ' ' . $data['lastname'] . '</option>';
				}
				$query->closeCursor();
				?>
				</select>
			</div>
			Ce que j'ai effectué :<br>
			<div class="form-group">
				<select id="activity" class="custom-select form-control" required onchange="stepShow(this.value);">
					<option selected>Sélectionnez une option ...</option>
					<option value="1">J'ai travaillé de manière rémunérée auprès d'un ou plusieurs employeurs ce qui m'a permis d’acquérir de l'expérience professionnelle</option>
					<option value="2">J'ai réalisé différentes démarches pour ma mobilité/santé, des dépots de candidatures, ou j'ai participé à des événements/ateliers</option>
					<option value="3">J'ai suivi une formation certifiante</option>
					<option value="4">J'ai effectué d'autres démarches</option>
					<option value="5">J'entre en Garantie Jeunes</option>
				</select>
			</div>
			<br>
			<div id="opt1">
				<h4>J'ai travaillé de manière rémunérée auprès d'un ou plusieurs employeurs ce qui m'a permis d'aquérir de l'expérience professionnelle</h4>
				<div class="form-group">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="i_st2_work">
						<label class="custom-control-label" for="i_st2_work">Je certifie avoir travaillé dans une (ou plusieurs) entreprise(s) et je joins à ma déclaration mon/mes dernier(s) salaire(s) et/ou contrat(s) de travail.</label>
					</div>
					<textarea id="st2_work" class="form-control" placeholder="Chez qui avez-vous travaillé ? Avec quel type de contrat ?"></textarea>
					<small class="form-text text-danger">Pensez à effectuer une copie de vos papiers ou d'en demander une pour les joindre à votre déclaration.</small>
				</div>
			</div>
			<div id="opt2">
				<h4>J'ai réalisé différentes démarches pour ma mobilité/santé, des dépots de candidatures, ou j'ai participé à des événements/ateliers</h4>
				<div class="form-group">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="i_st2_seminar">
						<label class="custom-control-label" for="i_st2_seminar">J'ai été présent à des ateliers et/ou des événements organisés.</label>
					</div>
					<textarea id="st2_seminar" class="form-control" placeholder="Comment s'appellent ces Ateliers/Événements ?"></textarea>
				</div>
				<div class="form-group">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="i_st2_steps">
						<label class="custom-control-label" for="i_st2_steps">J'ai réalisé des démarches liées à ma santé/la mobilité/le logement/autre.</label>
					</div>
					<textarea id="st2_steps" class="form-control" placeholder="Quelles sont les démarches réalisées ?"></textarea>
				</div>
				<div class="form-group">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="i_st2_applications">
						<label class="custom-control-label" for="i_st2_applications">J'ai déposé des candidatures pour des stages et/ou des emplois.</label>
					</div>
					<textarea id="st2_applications" class="form-control" placeholder="Où avez-vous déposé vos candidatures ? (Noms des entreprises)"></textarea>
				</div>
			</div>
			<div id="opt3">
				<h4>J'ai suivi une formation certifiante</h4>
				<div class="form-group">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="i_st2_education">
						<label class="custom-control-label" for="i_st2_education">Je certifie avoir suivi ou suivre une formation certifiante.</label>
					</div>
					<textarea id="st2_education" class="form-control" placeholder="Quelle est cette formation ? (Nom de la formation/du diplôme)"></textarea>
				</div>
			</div>
			<div id="opt4">
				<h4>J'ai effectué d'autres démarches</h4>
				<div class="form-group">
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input" id="r_st2_other" name="opt4_radio">
						<label class="custom-control-label" for="r_st2_other">J'ai effectué d'autres démarches.</label>
					</div>
					<textarea id="st2_other" class="form-control" placeholder="Quelles sont ces démarches ? (S'il s'agit d'un stage, écrire “STAGE :” suivi du nom de l'entreprise)"></textarea>
				</div>
				<div class="form-group">
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input" id="r_st2_nothing" name="opt4_radio">
						<label class="custom-control-label" for="r_st2_nothing">Je n'ai rien effectué.</label>
					</div>
					<textarea id="st2_nothing" class="form-control" placeholder="Pourquoi ?"></textarea>
				</div>
			</div>
			<div id="opt5">
				Afin de compléter au mieux votre déclaration, voici quelques informations à suivre correctement :<br>
				- Vous devez entrer le mois <strong>Précédent</strong> à celui en cours dans la case "MOIS"<br>
				- Vous devez déclarer une activité à <strong>0</strong> €<br><br>
				<div class="form-group">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="i_st2_new">
						<label class="custom-control-label" for="i_st2_new"> Vous devez cocher cette case pour passer à l'étape suivante</label>
					</div>
				</div>
			</div>
			<button class="btn btn-primary float-right" onclick="nextStep(1)">Suivant <i class="fad fa-arrow-right"></i></button>
		</div>
	</div>
	<div id="step3" class="container-fluid col-12 col-md-11 col-xl-10 card card-primary card-outline">
		<div class="card-header">
			<h3 class="card-title">Étape 3 - Mes revenus d'activité au cours du mois précédent</h3>
		</div>
		<div class="card-body">
			<div class="form-group">
				<div class="form-inline">
					<label for="st3_pay">Mes revenus d'activité au cours du mois précédent :</label>&nbsp;
					<input id="st3_pay" class="form-control" type="text" required autocomplete="off" style="width: 120px;"> <i class="fad fa-euro-sign"></i>
				</div>
				<small id="payHelp" class="form-text text-muted"><i class="fad fa-exclamation-circle"></i> L'allocation versée par la Garantie Jeunes n'est pas à déclarer.</small>
			</div><br>
			<strong>Sélectionnez le/les revenus perçus :</strong>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="st3_salaire">
				<label class="custom-control-label" for="st3_salaire">Salaire</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="st3_rsa">
				<label class="custom-control-label" for="st3_rsa">RSA</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="st3_ape">
				<label class="custom-control-label" for="st3_ape">Allocation Pôle Emploi</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="st3_pri_a">
				<label class="custom-control-label" for="st3_pri_a">Prime d'activité</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="st3_aah">
				<label class="custom-control-label" for="st3_aah">Allocation aux adultes handicapés</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="st3_pen_a">
				<label class="custom-control-label" for="st3_pen_a">Pension alimentaire</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="st3_rs">
				<label class="custom-control-label" for="st3_rs">Rétributions de stage (revenu)</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="st3_other">
				<label class="custom-control-label" for="st3_other">Autre(s) (Région, Bourse, etc...)</label><br>
			</div>
			<hr>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="certify" required>
				<label class="custom-control-label text-primary" for="certify">J'atteste sur l'honneur que les informations (et documents fournis) sont exactes, et je suis informé(e) des sanctions encourues en cas de fausse déclaration.</label>
			</div>
			<input type="text" id="type_key" disabled hidden value="<?php echo $link_type; ?>" required>
			<div class="clearfix mt-3">
				<button class="btn btn-secondary float-left" onclick="prevStep();"><i class="fad fa-arrow-left"></i> Précédent</button>
				<button class="btn btn-primary float-right" onclick="submit();">Valider <i class="fad fa-check"></i></button>
			</div>
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
				<strong>Garantie Jeunes © 2019 - <?php echo date('Y'); ?></strong><br>
				Ce site a été créé et codé pour la Mission Locale des Graves dans le cadre de la Garantie Jeunes.<br><br>
				<?php echo VERSION; ?>
			</p>
			<a href="../legals.php" class="card-link">Mentions légales</a>
		</div>
	</footer>
	<!-- start:JavaScripts -->
	<script defer src="./jscripts/jquery-3.4.1.min.js"></script>
	<script defer src="./jscripts/bootstrap.bundle.min.js"></script>
	<script defer src="./jscripts/sweetalert2.min.js"></script>
	<script defer src="./jscripts/toastr.min.js"></script>
	<script defer src="./jscripts/form_scr.min.js"></script>
</body>
</html>
