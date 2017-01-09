<?php
// declare(strict_types=1); // Muss die erste Anweisung in einer Datei sein
// includes
require_once '../inc/utilities.inc.php';
require_once '../inc/formconfig.inc.php';
require_once '../inc/db-connect.inc.php';
// zur DB verbinden
$db = connectDB('root', '', 'localhost', 'kurse');

// Initialisierung
$isSent = false;
$isAdded = false;
$isValid = false;
$hasErrros = false;
$isUpdated = false;
$errorMsg = '';
$formErrors = [];

// Form validieren
$isSent = isFormPosted();

if ($isSent) {
	$isValid = validateForm($formConfig, $formErrors);
	if ($isValid) {
		if(isset($_POST['button']) && $_POST['button'] == 'insert') {
			$sql = sql_insert($formConfig);
			$updRes = $db->query($sql);
			// echo $updRes."-erfolg?";
			// wurde etwas geändert, geben wir eine Meldung aus und setze hasUpdated auf true
			if ($db->affected_rows === 1) {
				$isAdded = true;
				foreach($_POST as $key => $val) {
					$_POST[$key]=''; // Formular löschen für weiteren Datensatz
				}
			} elseif ($db->error != '') {
				$errorMsg = $db->error;
			}
		}
		if(isset($_POST['button']) && $_POST['button'] == 'update') {
			$sql = sql_update($formConfig);
			$updRes = $db->query($sql);
			// echo $updRes."-erfolg?";
			// wurde etwas geändert, geben wir eine Meldung aus und setze hasUpdated auf true
			if ($db->affected_rows === 1) {
				$isUpdated = true;
			} elseif ($db->error != '') {
				$errorMsg = $db->error;
			}
		}
	}
	// dumpPre($formErrors);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8">
	<title>Registrierung</title>
	<link rel="stylesheet" href="../css/pure-min.css">
	<link rel="stylesheet" href="../css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/layout.css">
	<script src="../inc/myScript.js"></script>
</head>
<body>
<div class="wrapper">
	<header class="main-header">
		<h1>Kunden Verwaltung</h1>
	</header>
	<main>
<?php
if ($isAdded) {
 echo '<p>Datensatz wurde erfolgreich hinzugefügt.</p>' ;
}
if ($isUpdated) {
 echo '<p>Datensatz wurde erfolgreich aktualisiert.</p>' ;
}

if (!isset($_GET['add']) && !isset($_GET['del']) && !isset($_GET['edit'])) {
	include 'kunde-select.php';
}
if (isset($_GET['add'])) {
	include 'kunde-insert.php';
	}
if (isset($_GET['del'])) {
	include 'kunde-delete.php';
	}
if (isset($_GET['edit'])) {
	include 'kunde-update.php';
	}	
?>
</main>
	<footer>
	<a href="html/index.html">Projektdokumentation per Doxygen</a>
	</footer>
</div>
</body>
</html>