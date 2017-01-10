<?php
$sql = 'SELECT * FROM kunden;';
$res = $db->query($sql);
// var_dump($res);

//Prüfen ob die Query Einträge geliefert hat
if ($res->num_rows) {

	echo '<table class="pure-table pure-table-striped">';
	//Über die Einträge iterieren
	$rowNr = 0;
	while ($line = $res->fetch_assoc()) {
		// var_dump($line);
		// echo '<br>', $line['Tables_in_classicmodels'];

// Überschrift

		if ($rowNr === 0) {
			echo '<thead><tr>'	;
			foreach($line as $key => $val) {
				echo '<th>',dbToLabelName($key, $formConfig),'</th>';
			}
			echo '<th> </th><th> </th></tr></thead><tbody>';	
			$rowNr = 1;
		}	
// Datenzeilen erzeugen
	echo '<tr>'	;
		foreach($line as $key => $val) {
			echo '<td>',$val,'</td>';
		}
	echo '<td>';	
	echo '<a href="index.php?edit=', $line['kunden_id'], '">edit</a>';
	echo '</td>';	
	echo '<td>';	
	echo '<a href="index.php?del=', $line['kunden_id'], '">del</a>';
	echo '</td>';	
	echo '</tr>';	
	}
	echo '</tbody></table>';
} else {
	echo 'keine Daten gefunden';
}
	echo '<p><a href="index.php?add=1">Kunden hinzufügen</a></p>';
?>