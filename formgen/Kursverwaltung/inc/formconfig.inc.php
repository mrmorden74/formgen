<?php
/*
	Konfiguration der einzelnen Felder des Formulars.
	Die Variable wird an validateForm und makeFormFields übergeben.

	Eigenschaften eines Feldes (o ist optional):
	- fieldType 			text, checkbox, radio, select, ... könnte HTML damit erzeugen, derzeit nur "text"
	- label 				angezeigter Feldname(, Placeholder)
	- dataType 				bestimmt wie validiert wird z.b bool, int, float, email, regex ...
	- required 				Pflichtfeld oder nicht, true oder false
	- placeholder (o)		Standart= label, String wenn custom, "" für leer
	- minLength (o)			minimale Anzahl der Zeichen
	- maxLength (o)			maximale Anzahl der Zeichen
	- minVal 	(o)			Mindesthöhe bei Zahlenwerten
	- maxVal 	(o)			Maximale Höhe bei Zahlenwerten
	- preFix 	(o) 		Prefix für Inputfeld, wird auch in die Datenbank gespeichert
	- formatText (o) 		Pattern für Eingabe, derzeit nur "0" für Zahl umgesetzt
	- autoValue (o) 		Füllt Vorschlag aus, nur in Zusammenhang mit Int (auch Teil von Text) möglich
							(Startpunkt,Länge,Text wieder einfügen j/n)
	- edit (o) 				Standart = true, false Feld kann nicht bearbeitet werden							
	...
*/
$formConfig = [
	"kdnr" => [
		"fieldType" => "text",
		"label" => "Kundennummer",
		"dbName" => "kunden_kundennummer",
		"dataType" => "text",
		"required" => true,
		"placeholder" => "000000",
		"preFix" => "KdNr-",
		"minVal" => 1,
		"maxVal" => 999999,
		"formatText" => "000000",
		"autoValue" => "-6,6,j",
		"edit" => false
	],
	"kdvn" => [
		"fieldType" => "text",
		"label" => "Vorname",
		"dbName" => "kunden_vorname",
		"dataType" => "name",
		"required" => true,
	],
	"kdnn" => [
		"fieldType" => "text",
		"label" => "Nachname",
		"dbName" => "kunden_nachname",
		"dataType" => "name",
		"required" => true,
	],
	"kdadr" => [
		"fieldType" => "text",
		"label" => "Adresse",
		"dbName" => "kunden_adresse",
		"dataType" => "text",
		"required" => true,
	],
	"kdplz" => [
		"fieldType" => "text",
		"label" => "Postleitzahl",
		"dbName" => "kunden_plz",
		"dataType" => "text",
		"required" => true,
	],
	"kdort" => [
		"fieldType" => "text",
		"label" => "Ort",
		"dbName" => "kunden_ort",
		"dataType" => "text",
		"required" => true,
	],
	"kdtel" => [
		"fieldType" => "text",
		"label" => "Telefonnummer",
		"dbName" => "kunden_telefon",
		"dataType" => "phone",
		"required" => true,
	],
	"kdmail" => [
		"fieldType" => "text",
		"label" => "Email",
		"dbName" => "kunden_email",
		"dataType" => "email",
		"required" => true,
	]
];