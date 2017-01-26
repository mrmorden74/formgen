<?php
$sql = 'SELECT * FROM '.$formConfigAll['tblname'].';';
$res = $db->query($sql);
// var_dump($res);

//Prüfen ob die Query Einträge geliefert hat
if ($res->num_rows) {
	echo '<div class="table-responsive">';
	echo '<table class="table table-striped table-bordered">';
	//Über die Einträge iterieren
	$rowNr = 0;
	while ($line = $res->fetch_assoc()) {
		// var_dump($line);
		// echo '<br>', $line['Tables_in_classicmodels'];

// Überschrift

		if ($rowNr === 0) {
			echo '<thead><tr>'	;
			foreach($line as $key => $val) {
				if ($label = dbToLabelName($key, $formConfig)) {
					echo '<th>',$label,'</th>';
				}
			}
			echo '<th> </th><th> </th></tr></thead><tbody>';	
			$rowNr = 1;
		}	
// Datenzeilen erzeugen
	echo '<tr>'	;
		foreach($line as $key => $val) {
			if ($label = dbToLabelName($key, $formConfig)) {
				echo '<td>',getVal($key, $val, $formConfig[$key]),'</td>';
			}
		}
	echo '<td>';	
	echo '<a href="index.php?edit=', $line[$formConfigAll['primary']], '"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
	echo '</td>';	
	echo '<td>';	
	echo '<a href="index.php?del=', $line[$formConfigAll['primary']], '"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>';
	echo '</td>';	
	echo '</tr>';	
	}
	echo '</tbody></table></div>';
} else {
	echo 'keine Daten gefunden';
}
	echo '<p><a href="index.php?add=1">Datensatz hinzufügen</a></p>';
?>