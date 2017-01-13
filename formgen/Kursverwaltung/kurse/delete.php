<?php
if (isset($_GET['del']) && isset($_GET['ok'])) {
    $sql = 'DELETE FROM '.$formConfigAll['tblname'].' WHERE '.$formConfigAll['primary'].' = '.$_GET['del'].';';
    $res = $db->query($sql);
    if ($res) {
        echo 'Datensatz wurde gelöscht';
        header('location:index.php');
        exit;
    } elseif ($db->error != '') {
        $errorMsg = showError($db->errno);
        echo $errorMsg;
	}
} else {
    $sql = 'SELECT * FROM '.$formConfigAll['tblname'].' WHERE '.$formConfigAll['primary'].' = '.$_GET['del'].';';
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
                // echo '<thead><tr>'	;
                foreach($line as $key => $val) {
                    // echo '<th>',$key,'</th>';
                }
                // echo '<th> </th><th> </th></tr></thead><tbody>';	
                $rowNr = 1;
            }	
    // Datenzeilen erzeugen
                echo '<tbody>'	;
            foreach($line as $key => $val) {
                echo '<tr><td>',$key,'</td>';	
                echo '<td>',$val,'</td></tr>';	
            }
        }
        echo '</tbody></table>';
        echo '<p><a href="index.php?del=', $_GET['del'], '&ok=1">LÖSCHEN</a></p>';
    // Wenn keine EInträge vorhanden
    } else {
        echo 'keine Daten gefunden';
    }
}
	echo '<p><br><br><br><a href="index.php">Zurück zur Übersicht</a></p>';
?>