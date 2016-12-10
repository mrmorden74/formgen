<?php
    declare(strict_types=1);
    //die Klassen DB und SQL werden inkludiert
    require_once 'inc/class.db.inc.php';
    require_once 'inc/class.sql.inc.php';
    //speichert, ob der Insert erfogreich war
    $insertSuccess = false;
    //speichert die neue Kundennummer
    $neueKundenNummer='';
    //Objekt für die Verbindung zur Datenbank
    $dataBase = new DB();
    //Objekt für SQL-Abfragen
    $query = new SQL();
    //Verbindung zur Datenbank wird aufgebaut
    $conn = $dataBase->connectDB();
    //wurde ein POST-REQUEST gesendet
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sendPost'])) { 
      //war die Verbindung zur DB erfolgreich
      if($conn instanceof MySQLi) {
          //sql-Statement
          $sql ="INSERT INTO " . $dataBase->getTable() . " SET" .
          " kunden_kundennummer=\"KdNr-{$_POST['eNr']}\"" .
			    ", kunden_vorname=\"{$_POST['vn']}\""      .
			    ", kunden_nachname=\"{$_POST['nn']}\""     .
			    ", kunden_adresse=\"{$_POST['adr']}\""     .
			    ", kunden_plz=\"{$_POST['plz']}\""         .
			    ", kunden_ort=\"{$_POST['ort']}\""        . 
			    ", kunden_telefon=\"{$_POST['tele']}\""     .
			    ", kunden_email=\"{$_POST['em']}\"";
          //ist der Insert erfolgreich
          if($query->setQuery($sql, $conn)) {
            if($query->getAffectedRows($conn) === 1) {
              //wenn ja, dann setze insertSuccess auf true
              $insertSuccess = true;
            }
          }
          //Selektion der maximalen Kundennummer
          $sql = "SELECT MAX(kunden_kundennummer) AS max FROM " . $dataBase->getTable();
          $neueKundenNummer = $query->getNewCustomerNumber($sql, $conn);    
      }
    }
    else {//wurde kein POST-Request gesendet, wurde zum ersten Mal auf insert.php verwiesen und
          //es wird die maximale Kundennummer selektiert
        $sql = "SELECT MAX(kunden_kundennummer) AS max FROM " . $dataBase->getTable();
        $neueKundenNummer = $query->getNewCustomerNumber($sql, $conn);
    }
      //die Verbindung zur DB wird geschlossen
      mysqli_close($conn);
      //die Objekte database und query werden auf null gesetzt, weil sie nicht mehr benötigt werden
      $dataBase = null;
      $query = null;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <link rel="stylesheet" href="css/pure-min.css">
    <link rel="stylesheet" href="css/layout.css">
    <meta charset="UTF-8">
    <title>Kunden anlegen</title>
</head>
<body>
 <div id="wrapper">
  <header>
    <h1>Kunden anlegen</h1>
  </header>
  <main>
  <?php
    // wurde der Datensatz erfolgreich eingefügt, dann wird eine msg ausgegeben
    if($insertSuccess) echo "<div id=\"msg\">Kunde wurde erfolgreich angelegt</div>";
    //die Klasse Form wird inkludiert
    require_once 'inc/class.form.inc.php';
    //ein neues Objekt fuer ein Formular mit der POST-Methode wird erzeugt
    $form = new Form('POST');
    //die Ausgabe des Formulars wird begonnen
    echo $form->createForm();
    //Label fuer Kundennummer
    echo "<div class=\"pure-control-group\">\n";
    echo $form->addLabel('eNr', 'KdNr-:');
    //Input fuer neue Kundennummer
    echo $form->addFormField('text', 'eNr', 'eNr', ['6', "$neueKundenNummer"]);
    echo "</div>\n";
    echo "<div class=\"pure-control-group\">\n";
    //Label fuer Vorname
    echo $form->addLabel('vn', 'Vorname:');
    //Input fuer Vorname
    echo $form->addFormField('text', 'vn', 'vn', ['80', '']);
    echo "</div>\n";
    echo "<div class=\"pure-control-group\">\n";
    //Label fuer Nachname
    echo $form->addLabel('nn', 'Nachname:');
    //Input fuer Nachname
    echo $form->addFormField('text', 'nn', 'nn', ['120', '']);
    echo "</div>\n";
    echo "<div class=\"pure-control-group\">\n";
    //Label fuer Adresse
    echo $form->addLabel('adr', 'Adresse:');
    //Input fuer Adresse
    echo $form->addFormField('text', 'adr', 'adr', ['200', '']);
    echo "</div>\n";
    echo "<div class=\"pure-control-group\">\n";
    //Label fuer PLZ
    echo $form->addLabel('plz', 'PLZ:');
    //Input fuer PLZ
    echo $form->addFormField('text', 'plz', 'plz', ['4', '']);
    echo "</div>\n";
    echo "<div class=\"pure-control-group\">\n";
    //Label fuer Ort
    echo $form->addLabel('ort', 'Ort:');
    //Input fuer Ort
    echo $form->addFormField('text', 'ort', 'ort', ['80', '']);
    echo "</div>\n";
    echo "<div class=\"pure-control-group\">\n";
    //Label fuer Telefon
    echo $form->addLabel('tele', 'Telefon:');
    //Input fuer Telefon
    echo $form->addFormField('text', 'tele', 'tele', ['32', '']);
    echo "</div>\n";
    echo "<div class=\"pure-control-group\">\n";
    //Label fuer Email
    echo $form->addLabel('em', 'Email:');
    //Input fuer Email
    echo $form->addFormField('email', 'em', 'em', ['120', '']);
    echo "</div>\n";
    echo "<div class=\"pure-control-group\">\n";
    //Submit-Button
    echo $form->addFormField('submit', 'sendPost', 'sendPost', ['Datensatz einf&uuml;gen', '']);
    echo "</div>\n";
    //die Ausgabe fuer das Formular wird beendet
    echo $form->endForm();
    //das Form-Objekt wird auf null gesetzt, weil es nicht mehr benötigt wird
    $form = null;
  ?>
  </main>
 </div> 
</body>
</html>