<?php
$query = $db->prepare('SELECT * FROM gj_alerts WHERE active = "true" AND (position = "all" OR position = "admin")');
$query->execute();
$tmpl_alert = "";
while ($data = $query->fetch()) {
    $tmpl_alert .= '<div class="alert alert-'. $data['type'] .' alert-dismissible">';
    if ($data['dismissible'] == "true") {
        $tmpl_alert .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    }
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
    $tmpl_alert .= '<h5><i class="fad fa-'. $icon .'"></i> Information :</h5>';
    $tmpl_alert .= '<p class="mb-0">'. $data['content'] .'</p>';
    $tmpl_alert .= '</div>';
    echo $tmpl_alert;
    $tmpl_alert = "";
}
$query->closeCursor();
?>