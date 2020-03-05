<?php
require_once "./config.php";
if ($_POST["certify"] == true) {
	$query = $db->prepare('SELECT * FROM groups WHERE lastname = :lastname AND surname = :surname');
	$query->execute(array(
		'lastname' => $_POST['lastname'],
		'surname' => $_POST['surname']
	));
	$data = $query->fetch();
	if ($data > 0) {
		$current_date = date('Y-m-d H:i:s');
		if ($data['red_list'] == '1') {
			$notif = $db->prepare('INSERT INTO notifications (advisor_id, name, cohorte, time, readed) VALUES(:advisor, :name, :cohorte, :time, :read)');
			$notif->execute(array(
				'advisor' => $_POST['advisor'],
				'name' => $_POST['lastname'].' '.$_POST['surname'],
				'cohorte' => $_POST['group'],
				'time' => date($current_date),
				'read' => "false"
			));
			$notif->closeCursor();
			echo json_encode(array("msg" => "Vous ne pouvez pas valider de déclaration, votre conseiller(ère) veut d'abord vous parler. Une notification a été envoyée pour signaler votre présence.", "warn" => true));
		} else {
			$query = $db->prepare("SELECT * FROM activity_check WHERE surname = :surname AND lastname = :lastname ORDER BY update_date DESC");
			$query->execute(array(
				'lastname' => $_POST['lastname'],
				'surname' => $_POST['surname']
			));
			$data = $query->fetch();
			$query->closeCursor();
			if ($data['update_date'] >= date("Y-m-01") && $data['update_date'] <= date("Y-m-31")) {
				echo json_encode(array("msg" => "Vous avez déjà enregistré une déclaration ce mois-ci", "warn" => false));
			} else {
				# If user is not in the red list, we add his form in the database
				$insert = $db->prepare('
					INSERT INTO activity_check (surname, lastname, cohorte, month, year, advisor_id, activity, work, seminar, steps, applications, education, other, nothing, pay, chk_interim, chk_salaire, chk_rsa, chk_ape, chk_pri_a, chk_aah, chk_pen_a, chk_rs, chk_other, update_date, type_key, certif)
					VALUES(:surname, :lastname, :group, :month, :year, :advisor, :activity, :st2_work, :st2_seminar, :st2_steps, :st2_applications, :st2_education, :st2_other, :st2_nothing, :st3_pay, :st3_interim, :st3_salaire, :st3_rsa, :st3_ape, :st3_pri_a, :st3_aah, :st3_pen_a, :st3_rs, :st3_other, :update_date, :type_key, :certify)
				');
				// Just get current date to complete form
				$insert->execute(array(
					'surname' => $_POST['surname'],
					'lastname' => $_POST['lastname'],
					'group' => $_POST['group'],
					'month' => $_POST['month'],
					'year' => $_POST['year'],
					'advisor' => $_POST['advisor'],
					'activity' => $_POST['activity'],
					'st2_work' => $_POST['st2_work'],
					'st2_seminar' => $_POST['st2_seminar'],
					'st2_steps' => $_POST['st2_steps'],
					'st2_applications' => $_POST['st2_applications'],
					'st2_education' => $_POST['st2_education'],
					'st2_other' => $_POST['st2_other'],
					'st2_nothing' => $_POST['st2_nothing'],
					'st3_pay' => $_POST['st3_pay'],
					'st3_interim' => "false",
					'st3_salaire' => $_POST['st3_salaire'],
					'st3_rsa' => $_POST['st3_rsa'],
					'st3_ape' => $_POST['st3_ape'],
					'st3_pri_a' => $_POST['st3_pri_a'],
					'st3_aah' => $_POST['st3_aah'],
					'st3_pen_a' => $_POST['st3_pen_a'],
					'st3_rs' => $_POST['st3_rs'],
					'st3_other' => $_POST['st3_other'],
					'update_date' => $current_date,
					'type_key' => $_POST['type_key'],
					'certify' => $_POST['certify']
				));
				$insert->closeCursor();
				echo json_encode(array("msg" => true, "warn" => false));
			}
		}
	} else {
		echo json_encode(array("msg" => "Une erreur de saisie a été détectée.", "warn" => false));
	}
} else {
	echo json_encode(array("msg" => "La certification n'est pas validée !", "warn" => false));
}