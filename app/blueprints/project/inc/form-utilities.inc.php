<?php
/**
*	Fügt Formularfelder die im Array $conf mitgegebn werden ein und blendet Valiedierungsfehler aus $errors ein
*	@param $conf array
*	@param $errors array
*   @return string 
*/
function makeForm($conf,$errors) {
    //
}

function makeFormFields($conf, $errors) {
		$formFields = '';
	foreach ($conf as $fieldName => $fieldConf) {
		$formFields .= '<label for="'.$fieldName.'">'.$fieldConf['label']; // id-Name und sichtbarer Name des Eingabefelds
			if ($fieldConf['required'] === true) {
		$formFields .= "*";		
			}
			if (isset($errors[$fieldName])) {
		$formFields .=  '<span class="error"> --> '.$errors[$fieldName].'</span>'; // Fügt wenn vorhanden FehlerMeldung hinzu
			}
		$formFields .= '</label>';

		$formFields .= '<input type="'.$fieldConf['fieldType'].'"'; // Art des EIngabefeldes
		$formFields .= ' name="'.$fieldName.'" id="'.$fieldName.'"'; // (Variablen-)name des Eingabefeldes
			if ($fieldConf['placeholder']) {
				$formFields .= ' placeholder="'.$fieldConf['label'].'"'; // Optinale Verwendung des Platzhalters
			}
			if (!validate_empty($_POST[$fieldName])) {
				$formFields .= ' value="'.$_POST[$fieldName].'"'; // Fügt bereits eingegeben Wert nach POST ein (unabhängig der Gültigkeit)
			}
			if (isset($errors[$fieldName])) {
				$formFields .= ' class="error"'; // Formatiert den Text im EIngabefeld nach Fehler rot
			}
		$formFields .=  '>'	;
	}
	return $formFields;
}
