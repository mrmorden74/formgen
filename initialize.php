<?php
$init_success = false;
if (isset($_POST['myserver'])) {
  var_dump($_POST);
  if($con = create_con($_POST['myserver'],$_POST['user'],$_POST['pwd'])) {
    if($con = create_db($con,$_POST['dbname'])) {
      $handle = fopen("config_db.ini", "w");
        // devdb = "mysql:host=127.0.0.1;port=3306;dbname=formgen_"
        // devdbusername = "root"  
        // devdbpassword = ""
      $devdb = 'devdb = "mysql:host='.$_POST['myserver'].';port='.$_POST['port'].';dbname='.$_POST['dbname'].'"'. "\r\n";
      $devdbusername = 'devdbusername = "'.$_POST['user'].'"'. "\r\n";  
      $devdbpassword = 'devdbpassword = "'.$_POST['pwd'].'"'. "\r\n";
      fwrite($handle, '[globals]'."\r\n");
      fwrite($handle, $devdb);
      fwrite($handle, $devdbusername);
      fwrite($handle, $devdbpassword);
      fclose($handle);

      if (init_db_formgen($con)) {

        $init_success = true;
      }
    }
  close_dbcon ($con);
  }
}
if ($init_success)  {
$f3->config('config.ini');
$f3->config('config_db.ini');
// $f3->config('config_db_bak.ini');
$f3->config('routes.ini');

new Session();

$f3->run();
	
} 
 
if (!$init_success)  {

?>
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
    <p>Alle Daten des Formulargenerators werden in dieser Datenbank abgelegt. <br>Die Datenbank wird wenn Sie nicht existiert automatisch angelegt. Gleichzeitig werden die benötigten Tabellen erstellt. <br>Per Default werden die Benutzer admin/admin (mit Adminrechten) und user/userpw angelegt. <br>Ändern Sie die Passwörter so bald wie möglich.</p>
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