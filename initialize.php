<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Formgen</title>

    <!-- Bootstrap -->
    <link href="app/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="app/css/custom.css" rel="stylesheet">
  </head>
  <body>
<?php

 if (isset($_POST['myserver'])) {
	echo 'initialize all';
	$handle = fopen("config_db.ini", "w");
		// devdb = "mysql:host=127.0.0.1;port=3306;dbname=formgen_"
		// devdbusername = "root"  
		// devdbpassword = ""
		var_dump($_POST);
	$devdb = 'devdb = "'.$_POST['myserver'].';port='.$_POST['port'].';dbname='.$_POST['dbname'].'"'. "\r\n";
	$devdbusername = 'devdbusername = "'.$_POST['user'].'"'. "\r\n";  
	$devdbpassword = 'devdbpassword = "'.$_POST['pwd'].'"'. "\r\n";
	fwrite($handle, '[globals]'."\r\n");
	fwrite($handle, $devdb);
	fwrite($handle, $devdbusername);
	fwrite($handle, $devdbpassword);
	fclose($handle);
 } else {

?>
<div class="container">
  <h2>Vertical (basic) form</h2>
  <form method="POST">
    <div class="form-group">
      <label for="server">Server für Formulargeneratordaten:</label>
      <input type="text" class="form-control" id="server" name="myserver" placeholder="Server">
    </div>
    <div class="form-group">
      <label for="port">Portnummer:</label>
      <input type="text" class="form-control" id="port" name="port" placeholder="Port">
    </div>
    <div class="form-group">
      <label for="dbname">Datenbankname:</label>
      <input type="text" class="form-control" id="dbname" name="dbname" placeholder="Datenbankname">
    </div>
    <div class="form-group">
      <label for="user">Benutzername für Datenbank:</label>
      <input type="text" class="form-control" id="user" name="user" value="root" placeholder="Benutzername">
    </div>
    <div class="form-group">
      <label for="pwd">Password für Datenbank:</label>
      <input type="password" class="form-control" id="pwd" name="pwd" value="" placeholder="Enter password">
    </div>
    <button type="submit" class="btn btn-default">Submit</button>
  </form>
<?php
 }
?>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) 
    <script src="app/js/jquery-3.1.1.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="app/js/bootstrap.min.js"></script>
  </body>
</html>