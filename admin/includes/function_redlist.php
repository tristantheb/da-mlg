<?php
require "config.php";
date_default_timezone_set('Europe/Paris');
if (!isAdmin()) {
	$query = $db->prepare('SELECT * FROM notifications WHERE advisor_id = :advisor AND readed = "false"');
	$query->execute(array(
		'advisor' => $_COOKIE['advisor']
	));
} else {
	$query = $db->prepare('SELECT * FROM notifications WHERE readed = "false"');
	$query->execute();
}
$json['error'] = '0';
$start = date('H:i:s');
$time = date('Y-m-d H:i:s', strtotime('-5 minutes', strtotime($start)));
while ($data = $query->fetch()) {
	if ($data['time'] > $time) {
		$json['name'] = $data['name'];
		$json['alert'] = '1';
	} else {
		$json['alert'] = '0';
	}
}
$query->closeCursor();
// Return all json values
echo json_encode($json, JSON_UNESCAPED_SLASHES);
