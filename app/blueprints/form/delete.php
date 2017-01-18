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

        echo '<div class="container"><table class="table table-striped table-bordered">';
        //Über die Einträge iterieren
        $rowNr = 0;
        while ($line = $res->fetch_assoc()) {
            // var_dump($line);
            // echo '<br>', $line['Tables_in_classicmodels'];

    // Überschrift


    // Datenzeilen erzeugen
                echo '<tbody>'	;
            foreach($line as $key => $val) {
                echo '<tr><td>',dbToLabelName($key, $formConfig),'</td>';	
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
	echo '<p><br><a href="index.php">Zurück zur Übersicht</a></p></div>';
?>