<?php
/*
	Diverse Hilfsfunktionen
*/

/**
*	Ordnername wird als Titel ausgelesen
*   @return string 
*/
function getTitel() {
	$serverpfad = getenv('SCRIPT_FILENAME');
	$pfadarray = explode('/',$serverpfad);
	$count = count($pfadarray);
	$title = $pfadarray[$count-2];
	return $title;
}

/**
*	Ordnername wird als Titel ausgelesen
*   @return string 
*/
function getLinks() {
	$dir = dirname(getenv('SCRIPT_FILENAME'));
    if (isset($dir) && is_readable($dir)) { 
        $directionList = Array(); 
         
        $dir = realpath($dir); 
         
            $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST); 
             
        $x = 0; 
        foreach($objects as $entry => $object){     
            $getpath = str_replace($dir, '', $entry); 
            // $size = ceil($object->getSize()/1024); 
            // $changed = date($object->getMTime()); 

			$pfadarray = explode('\\',$getpath);
			$count = count($pfadarray);
			if ($count>=3) {
				$title = $pfadarray[2];
				if ($title == 'index.php') {
					// $directionList[$x]['path'] = $getpath; 
					// $directionList[$x]['size'] = $size; 
					// $directionList[$x]['changed'] = $changed;
					$links[$pfadarray[1]] =  $getpath;   
				}
			}
        $x ++; 
        } 
         
        return $links; 
    } 
} 


/**
*	Fügt pre Tag um var_dump ein
*	@param $val mixed
*/
function dumpPre($val) {
	echo '<pre>';
	var_dump( $val );
	echo '</pre>';
}

/**
*	Fügt Formularfelder die im Array $conf mitgegebn werden ein und blendet Valiedierungsfehler aus $errors ein
*	@param $conf array
*	@param $errors array
*	@param $type string TODO update oder insert; Gleichzeitig Label des Button
*   @return string 
*/
function makeFormFields($conf, $errors, $type) {
		$formFields = '	<form action="" method="post" class="pure-form pure-form-stacked">';
	foreach ($conf as $fieldName => $fieldConf) {
		$formFields .= '<label for="'.$fieldName.'">'.$fieldConf['label']; // id-Name und sichtbarer Name des Eingabefelds
			if ($fieldConf['required'] === true) {
		$formFields .= "*";		
			}
			if (isset($errors[$fieldName])) {
		$formFields .=  '<span class="error"> --> '.$errors[$fieldName].'</span>'; // Fügt wenn vorhanden FehlerMeldung hinzu
			}
		$formFields .= '</label>';
			if (isset($fieldConf['preFix'])){
		$formFields .= '<div class="align-left">'.$fieldConf['preFix'].'</div>';		
			}
		$formFields .= '<input type="'.$fieldConf['fieldType'].'"'; // Art des EIngabefeldes
		$formFields .= ' name="'.$fieldName.'" id="'.$fieldName.'"'; // (Variablen-)name des Eingabefeldes
					if (isset($fieldConf['placeholder'])) {
						$placeholder = $fieldConf['placeholder'];
					} elseif (isset($fieldConf['label'])) {
						$placeholder = $fieldConf['label'];
					}
		$formFields .= ' placeholder="'.$placeholder.'"'; // Optinale Verwendung des Platzhalters
			
			// echo count($_POST);
				$value = '';
			if (count($_POST)>0 && !validate_empty($_POST[$fieldName])) {
				$value = $_POST[$fieldName]; // Fügt bereits eingegeben Wert nach POST ein (unabhängig der Gültigkeit)
				if ($type == 'update' && isset($fieldConf['preFix'])) {
					$value = str_replace($fieldConf['preFix'], '' ,$value);
				}	
			} elseif (isset($fieldConf['autoValue'])) {
				// autoWert generieren
				$value = getAutowert($fieldConf['dbName'],$fieldConf['autoValue']); // Fügt bereits eingegeben Wert nach POST ein (unabhängig der Gültigkeit)
			}
			$formFields .= ' value="' . $value . '"';

			if (isset($errors[$fieldName])) {
				$formFields .= ' class="error"'; // Formatiert den Text im EIngabefeld nach Fehler rot
			}
			if (isset($fieldConf['edit']) && $type == 'update' && !$fieldConf['edit']) {
				$formFields .= ' readonly'; // Formatiert den Text im EIngabefeld nach Fehler rot
			}
		$formFields .=  '>'	;
	}
			// $formFields .=  '<input type="submit" value="Senden" class="pure-button pure-button-primary">';
			$formFields .=  '<button name="button" type="submit" value="'.$type.'" class="pure-button pure-button-primary button-right">'.$type.'</button>';
			$formFields .=  '</form>';
	return $formFields;
}
/***** Form Functions *****/
/**
*	Prüft, ob ein POST Request gesetzt wurde
*	@return boolean
*/
function isFormPosted() {
	return count($_POST) > 0;
}

/**
*	Validiert alle Felder, die im Array $conf mitgegeben werden.
*	@param $conf array
*	@param $errors Referenz auf außerhalb liegendes Array (pass by reference)
*	@return boolean
*/
function validateForm($conf, &$errors) {
	// 
	$formErrors = count($conf);

	// Schleife über config
	foreach ($conf as $fieldName => $fieldConf) {
		// Wert aus Formular ermitteln
		$fieldValue = '';
		if ( isset($_POST[$fieldName]) ) {
			$fieldValue = trim( $_POST[$fieldName] );
		}
		// required prüfen
		if ( $fieldConf['required'] === true && validate_empty($fieldValue) === true ) {
			// Fehler schreiben
			$errors[$fieldName] = 'Feld darf nicht leer sein';
			// Schleifendurchlauf des foreach abbrechen
			continue;
		}
		// Weitere Validierungen nur, wenn ein Wert gesendet wurde
		if ($fieldValue !== '') {
			// Alternative if/else Form. Wird verwendet, wenn die Werte einer Variable bekannt sind.
			switch( $fieldConf['dataType'] ) {
				case 'text':
					// kann alles enthalten; möglicherweise Länge beschränken
					break;
				case 'name':
					// keine Zahlen oder !"§$%&/()=?;:#+*- enthalten
					if ( validate_name($fieldValue) === false ) {
						// Fehler schreiben
						$errors[$fieldName] = 'Ungültiger Name';
						continue 2; // bricht foreach ab, zählt continues von innen nach außen
					}
					break;
				case 'kdnr':
					// keine Zahlen oder !"§$%&/()=?;:#+*- enthalten
					if ( validate_kdnr($fieldValue) === false ) {
						// Fehler schreiben
						$errors[$fieldName] = 'Ungültige Kundennummer';
						continue 2; // bricht foreach ab, zählt continues von innen nach außen
					}
					break;
				case 'email':
					if ( validate_email($fieldValue) === false ) {
						// Fehler schreiben
						$errors[$fieldName] = 'Ungültige E-Mail Adresse';
						continue 2; // bricht foreach ab, zählt continues von innen nach außen
					}
					break; // bricht den aktuellen case ab, geht aus switch raus
				case 'int':
					if ( validate_int($fieldValue) === false ) {
						// Fehler schreiben
						$errors[$fieldName] = 'Bitte nur Ganzzahlen eingeben';
						continue 2; // bricht foreach ab, zählt continues von innen nach außen
					}
					break;
				case 'float':
				    if ( validate_float($fieldValue) === false) {
						// Fehler schreiben
						$errors[$fieldName] = 'Bitte nur Floatzahlen eingeben';
						continue 2; // bricht foreach ab, zählt continues von innen nach außen
					}
					break;
				case 'number':
					break;
				case 'phone':
					// keine Zahlen oder !"§$%&/()=?;:#+*- enthalten
					if ( validate_phone($fieldValue) === false ) {
						// Fehler schreiben
						$errors[$fieldName] = 'Ungültiger Telefonnummer - Bitte im Format +43 0123 123456';
						continue 2; // bricht foreach ab, zählt continues von innen nach außen
					}
					break;
				case 'custom':
					break;
				case 'pw':
					if ( validate_pw($fieldValue) === false ) {
						// Fehler schreiben
						$errors[$fieldName] = 'Ungültiges Passwort - Zumindest Groß- und Kleinbuchstaben, Zahl und Sonderzeichen';
						continue 2; // bricht foreach ab, zählt continues von innen nach außen
					}
					break;
				default:
					break;
			}

			// restliche Validierungen, minLength, maxLength etc.
			if (isset($fieldConf['formatText']) && !validate_formTxt($fieldValue, $fieldConf['formatText'])) {
				// Fehler schreiben
				$errors[$fieldName] = 'Falsches Eingabeformat';
				continue; // bricht foreach ab, zählt continues von innen nach außen
			}
			if (isset($fieldConf['minLength']) && !validate_minLength($fieldValue, $fieldConf['minLength'])) {
				// Fehler schreiben
				$errors[$fieldName] = 'Der eingegebene Wert ist zu kurz';
				continue; // bricht foreach ab, zählt continues von innen nach außen
			}
			if (isset($fieldConf['maxLength']) && !validate_maxLength($fieldValue, $fieldConf['maxLength'])) {
				// Fehler schreiben
				$errors[$fieldName] = 'Der eingegebene Wert ist zu lang';
				continue; // bricht foreach ab, zählt continues von innen nach außen
			}
			if (isset($fieldConf['minVal']) && !validate_minVal($fieldValue, $fieldConf['minVal'])) {
				// Fehler schreiben
				$errors[$fieldName] = 'Der eingegebene Wert ist zu niedrig';
				continue; // bricht foreach ab, zählt continues von innen nach außen
			}
			if (isset($fieldConf['maxVal']) && !validate_maxVal($fieldValue, $fieldConf['maxVal'])) {
				// Fehler schreiben
				$errors[$fieldName] = 'Der eingegebene Wert ist zu hoch';
				continue; // bricht foreach ab, zählt continues von innen nach außen
			}



		} // ungleich Leerstring

		//
		$formErrors--;
	}
	// Wenn formErrors vorhanden sind ist das Formular nicht valide
	if ( $formErrors > 0 ) {
		// echo 'Fehler: ', $formErrors;
		return false;
	}

	// echo 'Fehlerfrei';
	return true;
}

/**
*	Validiert, ob der Wert kein Leerstring oder ungleich NULL ist
*	@param $val string
*	@return boolean
*/
function validate_empty($val) {
	if ($val === NULL || $val === '') {
		return true;
	}
	return false;
}

/**
*	Validiert, ob $val eine gültige E-Mail Adresse darstellt
*	@param $val string
*	@return boolean
*/
function validate_email($val) {
	if (filter_var($val, FILTER_VALIDATE_EMAIL) === false) {
		return false;
	}

	return true;
}

/**
*	Validiert, ob $val eine gültige Ganzzahl darstellt
*	@param $val mixed
*	@return boolean
*/
function validate_int($val) {
	if (filter_var($val, FILTER_VALIDATE_INT) === false) {
		return false;
	}

	return true;
}
/**
*	Validiert, ob $val vom Typ float ist
*	@param $val mixed
*	@return boolean
*/
function validate_float($val) {
	return is_float($val + 0);
}

/**
*	Validiert, ob $val keine für Namen ungültige Zeichen !?"§$%&/()=°^²³{[]}@€#|<>*-+,.;:-_\~ enthält
*	@param $val string
*	@return boolean
*/
function validate_name($val) {
 $chars = '!?"§$%&/()=°^²³{[]}@€#|<>*-+,.;:-_\\~';
return !search_charinstr($val,$chars);
}

/**
*	Validiert, ob $val keine für Namen ungültige Zeichen !?"§$%&/()=°^²³{[]}@€#|<>*-+,.;:-_\~ enthält
*	@param $val string
*	@return boolean
*/
function validate_pw($val) {
if (preg_match("`[A-Z]`", $val) &&
	preg_match("`[a-z]`", $val) &&
	preg_match("`[0-9]`", $val) &&
	preg_match("'[^A-Za-z0-9]'", $val)) {
//    echo 'String enthält auch andere Zeichen.'; 
   return true; 
} 
   return false; 
}
/**
*	Validiert, ob $val keine für Namen ungültige Zeichen !?"§$%&/()=°^²³{[]}@€#|<>*-+,.;:-_\~ enthält
*	@param $val string
*	@return boolean
*/
function validate_kdnr($val) {
if (preg_match("`KdNr-`", $val) &&
	preg_match("`[0-9]`", $val)) {
//    echo 'String enthält auch andere Zeichen.'; 
   return true; 
} 
   return false; 
}
/**
*	Validiert, ob $val eines der Zeichen aus $chars enthält
*	@param $val string
*	@param $chars string
*	@return boolean
*/
function search_charinstr($val,$chars) {
	foreach(str_split($chars) as $char) {
		// echo $char,'<br>';
		if (strpos($val, $char) !== false) {
		// echo strpos($val, $char),'<br>';
		return true;
		}
	}
return false;
}

/**
*	Validiert, ob $val nur Zahlen,+ und Leerzeichen enthält
*	@param $val string
*	@return boolean
*/
function validate_phone($val) {
if (!preg_match("#^[0-9 +]+$#", $val)) {
//    echo 'String enthält auch andere Zeichen.'; 
   return false; 
} 
   return true; 
}

/**
*	Validiert, ob $val kürzer als die Minimalvorgabe ist
*	@param $val mixed
*	@param $length int
*	@return boolean
*/
function validate_minLength($val,$length) {
	if (strlen(trim($val)) < $length) {
		return false;
	}

	return true;
}

/**
*	Validiert, ob $val länger als die Maximalvorgabe ist
*	@param $val mixed
*	@param $length int
*	@return boolean
*/
function validate_maxLength($val,$length) {
	if (strlen(trim($val)) > $length) {
		return false;
	}

	return true;
}

/**
*	Validiert, ob $val Wert unter minwert ist
*	@param $val int, float
*	@param $minval int
*	@return boolean
*/
function validate_minVal($val,$minval) {
	if ($val < $minval) {
		return false;
	}

	return true;
}

/**
*	Validiert, ob $val Wert über maxwert ist
*	@param $val int, float
*	@param $maxval int
*	@return boolean
*/
function validate_maxVal($val,$maxval) {
	if ($val > $maxval) {
		return false;
	}

	return true;
}

/**
*	Validiert, ob $val dem vorgegebenen Format entspricht
*	@param $val string
*	@param $format string
*	@return boolean
*/
function validate_formTxt($val,$format) {
	// Aus $format regulären Ausdruck erzeugen
	$fchar = '(';
	foreach(str_split($format) as $char) {
		switch($char) {
			case '0':
				// für jede Stelle mit einer Zahl;
				$fchar .= '[0-9]';
				break;
			default:
				break;
		}				 
	}
	$fchar .= ')';
	// Überprüfung der Länge und der Formatvorlage
	if (validate_minLength($val,strlen($format)) && 
		validate_maxLength($val,strlen($format)) &&
		preg_match($fchar, $val)) {
		//  String stimmt überein 
   		return true; 
	} 
   		return false; 
	}

/**
*	Erzeugt Sql Insert auf Basis der config Datei
*	@param $conf array
*	@return string sql-Statement
*/
function sql_insert($conf) {
// neu in PHP 7: non coallescing operator: wenn $_GET != NULL ist und Wert hat, sonst ...

	// Validierung, vorläufig ist alles OK
	$sql = 'INSERT INTO kunden SET ' ;
	$komma = '';
	foreach ($conf as $fieldName => $fieldConf) {
		// echo var_dump($conf);
		$sql .= $komma.$fieldConf['dbName'].'="';
			if (isset($fieldConf['preFix'])) {
		$sql .= $fieldConf['preFix'];
			} 
		$sql .= $_POST[$fieldName] . '"';	
		// $sql .= utf8_encode($_POST[$fieldName]) . '"';	
		$komma = ", ";
	}
	$sql .= ';';
return $sql;			
}
/**
*	Erzeugt Sql Update auf Basis der config Datei
*	@param $conf array
*	@return string sql-Statement
*/
function sql_update($conf) {
// neu in PHP 7: non coallescing operator: wenn $_GET != NULL ist und Wert hat, sonst ...

	// Validierung, vorläufig ist alles OK
	$sql = 'UPDATE kunden SET ' ;
	$komma = '';
	foreach ($conf as $fieldName => $fieldConf) {
		// echo var_dump($conf);
		$sql .= $komma.$fieldConf['dbName'].'="';
			if (isset($fieldConf['preFix'])) {
		$sql .= $fieldConf['preFix'];
			} 
		$sql .= $_POST[$fieldName] . '"';	
		// $sql .= utf8_encode($_POST[$fieldName]) . '"';	
		$komma = ", ";
	}
	$sql .= 'WHERE kunden_id = '.$_GET['edit'];
	$sql .= ';';
return $sql;			
}
/**
*	Erzeugt Autowert
*	@param $fieldName string Name des Feldes, welches mittels autoincrediment befüllt werden soll
*	@param $def string (STart,Länge,Wert wieder in Text einbauen j/n)
*	@param $data ??? Woher
*	@return mixed
*/
function getAutowert($fieldName, $def) {
 $db = connectDB('root', '', 'localhost', 'kurse');
 $sql = 'SELECT MAX(SUBSTR(`kunden_kundennummer`,length(`kunden_kundennummer`)-5,6)) AS "MAXVALUE" FROM `kunden`';
 $res = $db->query($sql);
    if ($res->num_rows) {
        while ($line = $res->fetch_assoc()) {
            foreach($line as $key => $val) {
                // echo dumpPre($line),$key," - ",$val;
                // echo "-",dbToPostName($key, $formConfig);
                // $maxValue = $val;
				$maxValue = sprintf('%06d', $val+1);
            }    
        }    
    } else {
        $maxValue = '000001';
    }
	return $maxValue;
}

/**
*	Liefert custom Fehlermeldung
*	@param $err Fehlernummer
*	@return string
*/
function showError($err) {
	switch( $err ) {
		case '1451':
			$errorMsg = 'Kunde hat bereits Kurs gebucht. Stornieren Sie zuerst die bestehenden Kursbuchungen des Kunden.';
			break;
		default:
			$errorMsg = $db->error;
			break;
	}
	return $errorMsg;
}

/**
*	Wandelt den Feldname aus der Datenbank in jenen des POST um 
*	@param $strg Name laut Konfigurationsdatei
*	@param $conf Array Konfigdatei
*	@return string
*/
function confToPostName($strg, $conf) { 
	foreach ($conf as $fieldName => $fieldConf) {
		if ($strg === $fieldConf['dbName']) {
			// echo $strg,' - ',$fieldName,' - ';
			return $fieldName;
		}
	}
}

/**
*	Wandelt den Feldname aus der Datenbank in jenen des Formulars um 
*	@param $strg Name laut Datenbank
*	@param $conf Array Konfigdatei
*	@return string
*/
function dbToLabelName($strg, $conf) { 
	foreach ($conf as $fieldName => $fieldConf) {
		if ($strg === $fieldConf['dbName']) {
			// echo $strg,' - ',$fieldName,' - ';
			return $fieldConf['label'];
		}
	}
}


