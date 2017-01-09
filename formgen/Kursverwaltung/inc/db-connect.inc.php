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
?>
