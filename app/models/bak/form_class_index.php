<?php
    declare(strict_types=1);
    /** wenn das Aktions-Formular abgeschickt wurde, 
        wird durch den header auf die richtige Seite 
        verwiesen.
    */
    if(($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['sendPost']))) {
        switch($_POST['action']) {
            case 'Insert' : header('Location: insert.php');break;
            case 'Update' : header('Location: inc/update.inc.php');break;
            case 'Delete' : header('Location: inc/delete.inc.php');break;
            default       : break;
        }
    }
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <link rel="stylesheet" href="css/pure-min.css">
    <link rel="stylesheet" href="css/layout.css">
    <meta charset="UTF-8">
    <title>Kunden-Verwaltung</title>
</head>
<body>
 <div id="wrapper">
    <header>
        <h1>Kunden-Verwaltung</h1>
    </header>
    <main>
        <?php
        //die Klasse Form wird inkludiert
        require_once 'inc/class.form.inc.php';
        //ein neues Objekt fuer ein Formular mit der POST-Methode wird erzeugt
        $form = new Form('POST');
        //die Ausgabe des Formulars wird begonnen
        echo $form->createForm();
        echo "<div class=\"pure-control-group\">\n";
        //Label fuer die radio-button-gruppe 'action'
        echo $form->addLabel('action', 'Aktion ausw&auml;hlen:');
        //radio-button-gruppe 'action'
        echo $form->addFormField('radio', 'action', 'action', ['Insert', 'checked']);
        echo $form->addFormField('radio', 'action', 'action', ['Update', '']);
        echo $form->addFormField('radio', 'action', 'action', ['Delete', '']);
        //Submit-Button
        echo $form->addFormField('submit', 'sendPost', 'sendPost', ['Aktion ausw&auml;hlen']);
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