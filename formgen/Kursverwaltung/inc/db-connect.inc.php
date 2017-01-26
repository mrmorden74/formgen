<?php
/** 
*	Verbindet sich zu mysql und gibt ein mysqli Objekt zurück
*	@param $user string 	Username
*	@param $pw string 		Username
*	@param $host string 	Username
*	@param $db string 		Username
*
*/
function connectDB(string $user, string $pw, string $host, string $db) : mysqli {
	
	// echo 'connect: ', $user,'-', $pw,'-', $host,'-', $db;
	$mysqli = new mysqli($host, $user, $pw, $db);
	// var_dump($mysqli->connect_errno);
	if ($mysqli->connect_errno) {
		// TODO: in Log-FIle schreiben
		echo 'Fehler beim Verbinden zur Datenbank.: ', $mysqli->connect_errno;
	}
	// damit die Daten auch in phpmyadmin korrekt eingetragen werden
	// mysqli verwendet nicht die Kollation der Datenbank, sondern italian
	// TODO Kollation der DB holen und verwenden
	// $mysqli->query( 'SET NAMES utf8' );
	// var_dump($mysqli->get_charset()); // vor dem Umsetzen
	$mysqli->set_charset("utf8");
	// var_dump($mysqli->get_charset()); // nach dem Umsetzen 
	return $mysqli;
}

/**
*  Holt sich Zugangsdaten aus der dbconfig
*  return array Zugangsdaten
*/
function getConDb() {
	$string = explode(',', file_get_contents('../config/dbconfig.csv', true));
	// var_dump ($string);
	return array(
		"user" => $string[3]  ,
  		"pw" => $string[4]  ,
		"host" => $string[1]  ,
		"db" => $string[5]  
	);
}

/**
*  Holt sich Projektname aus der dbconfig
*  return string Projektname
*/
function getProjectName() {
	$string = explode(',', file_get_contents('../config/dbconfig.csv', true));
	// var_dump ($string);
 	return $string[6];
}

/**
*  Holt sich ServerId aus der dbconfig
*  return string ServerId
*/
function getSrvId() {
	$string = explode(',', file_get_contents('config/dbconfig.csv', true));
	// var_dump ($string);
 return $string[0];
}

/**
*  Holt sich DatenbankId aus der dbconfig
*  return string DatenbankId
*/
function getDbId() {
	$string = explode(',', file_get_contents('config/dbconfig.csv', true));
	// var_dump ($string);
 return $string[7];
}

?>
