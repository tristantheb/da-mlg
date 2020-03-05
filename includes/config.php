<?php
error_reporting(0); // 0 = NONE / -1 = ALL
ini_set("display_errors", 0); // 0 or 1 (boolean)

const VERSION = "MLG-DA.v1.1.2";

function db_connect() {
	try {
		$pdo_options = array (
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
			PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT
			//PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);
		$host = 'localhost';
		$dbname = 'activity_declare';
		$user = 'root';
		$password = '';

		$db = new PDO('mysql:host='.$host.';dbname='.$dbname.'', $user, $password, $pdo_options);
		return $db;
	} catch (Exception $e) {
		die('Erreur de connexion : ' . $e->getMessage());
	}
}

session_start();
$db = db_connect();
include "function_tasks.php";