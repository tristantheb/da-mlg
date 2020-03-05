<?php
error_reporting(0); // 0 = NONE / -1 = ALL
ini_set("display_errors", 0); // 0 or 1 (boolean)

const VERSION = "MLG-DA.v1.1.2_admin";

function db_connect()
{
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

// Check if the user session is logged
function isLogged()
{
	return isset($_SESSION['logged']);
}

// Check if the user session is set
function isIdentified()
{
	return isset($_COOKIE['advisor']);
}

// Check if user adm level is 1 and set
function isAdmin()
{
	return isset($_COOKIE['is_admin']);
}

session_start();
// Set the database on the $db var
$db = db_connect();

function count_stats(string $table, string $options, bool $month = false) {
	if ($month == true) {
		$date_st = date('Y-m-01 00:00:00');
		$date_ed = date('Y-m-31 23:59:59');
		$options .= 'AND (update_date > "'.$date_st.'" AND update_date < "'.$date_ed.'")';
	}
	$query = db_connect()->prepare("SELECT * FROM $table WHERE $options");
	$query->execute();
	$count = $query->rowCount();
	$query->closeCursor();
	return $count;
}

function count_duration(string $date) {
	$date_current = date_create($date);
	$date_converted = time() - date_timestamp_get($date_current);
    $chunks = array(
        array(60 * 60 * 24 * 365, 'an'),
        array(60 * 60 * 24 * 30, 'mois'),
        array(60 * 60 * 24 * 7, 'semaine'),
        array(60 * 60 * 24, 'jour'),
        array(60 * 60, 'heure'),
        array(60, 'minute'),
        array(1, 'seconde')
    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($date_converted / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? 'il y a 1 '.$name : "il y a $count {$name}s";
    return $print;
}