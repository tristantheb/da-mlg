<?php
require_once "./config.php";
/**
 * Check values in database in DESC mode (it's possible to have 2 occurences, we need only the most recent)
 */
if ($_POST['surname'] && $_POST['lastname']) {
    $query = $db->prepare("SELECT * FROM groups WHERE surname = :surname AND lastname = :lastname ORDER BY id DESC");
    $query->execute(array(
        'surname' => $_POST['surname'],
        'lastname' => $_POST['lastname']
    ));
    $data = $query->fetch();
    $query->closeCursor();
    if ($data > 0) {
        if ($data['active'] == 'true') {
            echo json_encode(array("advisor" => $data['advisor_id'], "group" => $data['cohorte'], "error" => ""));
        } else {
            echo json_encode(array("advisor" => "", "group" => "", "error" => "Ce dossier a été désactivé. Impossible de compléter la déclaration d'activité."));
        }
    } else {
        echo json_encode(array("advisor" => "", "group" => "", "error" => "Aucun dossier ne semble correspondre ! Vérifiez que vous écrivez correctement votre NOM et votre Prénom."));
    }
} else {
    echo json_encode(array("advisor" => "", "group" => "", "error" => "Vous n'avez pas complété votre nom et/ou prénom."));
}