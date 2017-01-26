<?php
// declare(strict_types=1); // Muss die erste Anweisung in einer Datei sein
// includes
require_once '../inc/utilities.inc.php';
$titel = getTitel();
require_once './'.$titel.'.php';
require_once '../inc/db-connect.inc.php';
require_once '../inc/cryptor.php';
// zur DB verbinden
$dbcon = getConDb();
$db = connectDB($dbcon['user'], $dbcon['pw'], $dbcon['host'], $dbcon['db']);
// Initialisierung

$formConfig = $formConfigAll['fields'];
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
			$sql = sql_insert($formConfigAll);
			$updRes = $db->query($sql);
			// echo $updRes."-erfolg?";
			// wurde etwas geändert, geben wir eine Meldung aus und setze hasUpdated auf true
			if ($db->affected_rows === 1) {
				$isAdded = true;
				foreach($_POST as $key => $val) {
					$_POST[$key]=''; // Formular löschen für weiteren Datensatz
				}
			} elseif ($db->error != '') {
				echo 'TEST';
				$errorMsg = $db->error;
			}
		}
		if(isset($_POST['button']) && $_POST['button'] == 'update') {
			$sql = sql_update($formConfigAll);
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
	<title><?= $formConfigAll['frmname'] ?></title>
	<!--<link rel="stylesheet" href="../css/pure-min.css">-->
	<link rel="stylesheet" href="../css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/layout.css">
	<link rel="stylesheet" href="../css/custom.css">
	<link rel="stylesheet" href="../css/jquery.simple-dtpicker.css">
</head>
<body>
	<header>
		<div class="container-fluid">
			<div class="container">
				<h1><?= $formConfigAll['frmname'] ?>
			</h1>
			</div>
		</div>
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
	include 'select.php';
}
if (isset($_GET['add'])) {
	include 'insert.php';
	}
if (isset($_GET['del'])) {
	include 'delete.php';
	}
if (isset($_GET['edit'])) {
	include 'update.php';
	}	
?>

</main>
<footer class="container">
	<!--<a href="html/index.html">Projektdokumentation per Doxygen</a>-->
	<a href="../index.php">zum Index</a>
</footer>
<script src="../js/myScript.js"></script>
<script src="../js/jquery-3.1.1.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.simple-dtpicker.js"></script>
<?php
echo $script;
?>
</body>
</html>