<?php
    // Verbindung testen
    // Create connection
function create_con ($server, $user, $pwd, $db = NULL, $port = 3306) {
    $conn = new mysqli($server, $user, $pwd, $db, $port);
    // Check connection
    if ($conn->connect_error) {
        //TODO errorhandling doesn't work'
        $valid=[];
        $valid[]= "Connection failed: " . $conn->connect_error; 
        $f3->set('validdb',$valid);;
        // $this->addDbForm(); 
        return false;
    }
	return $conn;
} 

    // Verbindung testen
    // Create connection
function show_db ($conn) {
    $sql="SHOW DATABASES";

    if (!($result=mysqli_query($conn,$sql))) {
        printf("Error1: %s\n", mysqli_error($conn));
        return false;
    }
	return $result;
} 
 
    // Überprüfen ob Datenbank existiert
    // Bei Bedarf Erstellen
    // Create database
function create_db ($conn, $dbname) {
    $sql = "CREATE DATABASE ".$dbname;
    if ($conn->query($sql) === TRUE) {

    } else {
        $valid=[];
        if ($conn->errno <> '1007') {
            // $this->addDbForm(); 
            $valid[]= "Error creating database: " . $conn->errno; 
            $f3->set('validdb',$valid);
            return false;
        }
            $valid[]= "Database allready exists: " . $conn->errno; 
            // $f3->set('validdb',$valid);
    }
	$conn->select_db($dbname);
    return $conn;  
}   

function init_db_formgen($conn) {
    $sql = file_get_contents( "app/inc/formgen.sql" );

    if ($conn->multi_query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function close_dbcon ($conn) {
    $conn->close();   
}

function create_folder ($root,$data) {
    var_dump ($data);
    // Ordner mit config erzeugen
    $path = $root.'\\formgen\\'.$data['projectname'].'\\config';
    $chmod = 0777;
    if(!(is_dir($path) OR is_file($path) OR is_link($path) )) { 
        // echo $path;
        mkdir ($path,$chmod,true); 
    } else {
        // $valid=[];
        // $valid[]= "Ordner schon vorhanden"; 
        // $f3->set('validdb',$valid);
        // $this->addDbForm(); 
        // return false;
    } 
    $fp = fopen($path.'\\dbconfig.csv', 'w');
    fputcsv($fp, $data);
    echo $fp;
    fclose($fp);
    $source = $root.'\\app\\blueprints\\project';
    $dest = $root.'\\formgen\\'.$data['projectname'];
    $files = xcopy($source, $dest);
	return true;
}

/**
 * Copy a file, or recursively copy a folder and its contents
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @param       int      $permissions New folder creation permissions
 * @return      bool     Returns true on success, false on failure
 */
function xcopy($source, $dest, $permissions = 0755)
{
    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest, $permissions);
    }

    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        xcopy("$source/$entry", "$dest/$entry", $permissions);
    }

    // Clean up
    $dir->close();
    return true;
}
/**
*	Exportiert Daten in ein Textfile. DIe Dtane im File werden überschrieben
*	@param $filename string Name des Files ohne Endung
*	@param $path string kompletter Pfad ohne Datei, Ende ohne "\\"
*	@param $data mixed Inhalt für Textfile
*	@param $format string Umwandlungsmethode
*            csv - putcsv - Dateiendung .csv wird erzeugt
*            ser - serialize - Dateiendung .ser wird erzeugt
*            json - json_encode(PRETTY) - Dateiendung .json wird erzeugt
*   @return bool 
*/
function export_file ($filename,$path,$data,$format='csv') {
    // Ordner erzeugen
    $chmod = 0777;
    if(!(is_dir($path) OR is_file($path) OR is_link($path) )) { 
        mkdir ($path,$chmod,true); 
    } 
    //filename und Exportdatenumwandlung
    switch ($format) {
        case 'ser':
            $fp = fopen($path.'\\'.$filename.'.ser', 'w');
            $data_export = serialize ($data);
        break;
        case 'json':
            $fp = fopen($path.'\\'.$filename.'.json', 'w');
            $data_export = json_encode($data,JSON_PRETTY_PRINT);
        break;
        case 'array':
            $fp = fopen($path.'\\'.$filename.'.php', 'w');
            var_dump($data);
        break;
        default:
            # code...
            break;
    }
    //File schreiben und schließen
    fwrite($fp, $data_export);
    fclose($fp);
    echo $data_export;
    // TODO: Errorhandling 
	return true;
}

function update_db ($dbname) {    

}

function show_tables ($conn,$dbname = NULL) {
    if (is_null($dbname)) {
        $sql="SELECT database() AS activ_db";
        if (!($resultdb=mysqli_query($conn,$sql)->fetch_row())) {
            printf("Error2: %s\n", mysqli_error($conn));
            return false;
        }
        $dbname = $resultdb[0];
    }
    $sql="SHOW TABLES FROM ".$dbname;
    if (!($result=mysqli_query($conn,$sql))) {
    // echo $dbname;
        // echo $dbname;
        // printf("Error3: %s\n", mysqli_error($conn));
        $valid[]= "Error3: ". $result->error; 
        var_dump ($valid);
        // $f3->set('validdb',$valid);
       return false;
    }
    $tables = $result->fetch_assoc();
    // echo $tables;
	return $result;
}
