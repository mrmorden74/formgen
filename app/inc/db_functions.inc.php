<?php
    // Verbindung testen
    // Create connection
function create_con ($server, $user, $pwd, $db = NULL, $port = 3306) {
    set_error_handler("customError");     
    $conn = new mysqli($server, $user, $pwd, $db, $port);
    // Check connection
    if ($conn->connect_error) {
        //TODO errorhandling doesn't work'
        // $valid=[];
        // $valid[]= "Connection failed: " . $conn->connect_error; 
        // $f3->set('validdb',$valid);;
        // $this->addDbForm(); 
        return false;
    }
	return $conn;
} 

function customError($errno = 1, $errstr = "!!! ERROR !!!") {
//   echo "Ending Script";
        // $valid=[];
        // $valid[]= "<b>Error:</b> [$errno] $errstr<br>"; 
        return false;
        // $this->f3->set('validdb',$valid);;
        // $this->addDbForm(); 
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
    // var_dump ($data);
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
    // echo $fp;
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
//  echo $filename,$path,$data,$format;
    // Ordner erzeugen
    $chmod = 0777;
    if(!(is_dir($path) OR is_file($path) OR is_link($path) )) { 
        mkdir ($path,$chmod,true); 
    } 
    //filename und Exportdatenumwandlung
    switch ($format) {
        case 'ser':
            $filename = $filename.'.ser';
            $data_export = serialize ($data);
        break;
        case 'json':
            $filename = $filename.'.json';
            $data_export = json_encode($data,JSON_PRETTY_PRINT);
        break;
        case 'array':
            $filename = $filename.'.php';
            $data_export = mkArrayPhpCode($data,'$formConfigAll');
        break;
        default:
            # code...
            break;
    }
    //File schreiben und schließen
    // echo $filename;
    $fp = fopen($path.'\\'.$filename, 'w');
    fwrite($fp, $data_export);
    fclose($fp);
    // echo $data_export;
    // TODO: Errorhandling 
	return true;
}

function mkArrayPhpCode ($data, $name) {
    $data_export = "<?php\n\n$name = [\n";
    if (!is_array($data)) {
        $data_export .= "\"$data\",\n";       
    } else {
        foreach ($data as $key => $value) {
            $data_export .= "\t\"$key\" => ";
            if (!is_array($value)) {
                $data_export .= "\"$value\",\n";       
            } else {
                $data_export .= "[\n";
                foreach ($value as $key2 => $value2) {
                    $data_export .= "\t\t\"$key2\" => ";
                    if (!is_array($value2)) {
                        $data_export .= "\"$value2\",\n";       
                    } else {
                        $data_export .= "[\n";
                        foreach ($value2 as $key3 => $value3) {
                            // echo $key3.' => '.$value3.'\n';
                            $data_export .= "\t\t\t\"$key3\" =>";
                            if (!is_array($value3)) {
                                $data_export .= set_typ($value3).",\n";  
                            }
                        }
                        $data_export .= "\t\t],\n";
                    }
                }
                $data_export .= "\t],\n";
            }
        }
    }
    $data_export .= "];\n";
    // echo $data_export;
    // var_dump($data);
    return $data_export;
}

function set_typ ($value) {
    $vartype = gettype ($value);
    switch ($vartype) {
        case 'boolean':
            $fill = '';
            $boolval = 'false';
            if ($value = 1) $boolval = 'true';
            $retval = $fill.$boolval.$fill;
            break;
        case 'integer':
        case 'double':
            $fill = '';
            $retval = $fill.$value.$fill;
            break;
        case 'string':
            $fill = '"';
            $retval = $fill.$value.$fill;
            break;
        default:
            $fill = '"';
            break;
    }
    return $retval;
}

function update_db ($dbname) {    

}

/**
*  Liefert alle Tabellen einer Datenbank als MySqli Objekt
*  param $conn	mysqli 	MySqliConnection
*  param $dbname	string	Datenbankname, NULL wird aktive Datenbank genommen oder Fehler
*  return object    mysqli Object
*/
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
        // printf("Error3: %s\n", mysqli_error($conn));
        $valid[]= "Error3: ". $result->error; 
        var_dump ($valid);
        return false;
    }
	return $result;
}

function insert_table () {

}