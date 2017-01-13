<?php
	// Formularfelder einfügen
    $fieldData = [];
    $sql = 'SELECT * FROM '.$formConfigAll['tblname'].' WHERE '.$formConfigAll['primary'].' = '.$_GET['edit'].';';
    $res = $db->query($sql);
    if ($res->num_rows) {
        while ($line = $res->fetch_assoc()) {
            foreach($line as $key => $val) {
                // echo dumpPre($line),$key," - ",$val;
                // echo "-",dbToPostName($key, $formConfig);
                $fieldData[confToPostName($key, $formConfig)] = $val;
            }    
        }    
    } else {
        echo 'keine Daten gefunden';
    }
    $_POST = $fieldData;
    // dumpPre($fieldData);
	$formFields = makeFormFields($formConfig,$formErrors,'update');
	echo $formFields;
?>
<p class="error"><?php echo $errorMsg; ?></p>
<a href="index.php">zurück zur Übersicht</a>
