<?php
    // Verbindung testen
    // Create connection
function create_con ($server, $user, $pwd, $port = 3306) {
    $conn = new mysqli($server, $user, $pwd,null,$port);
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
        printf("Error: %s\n", mysqli_error($link));
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

function create_folder ($dbname) {
    // Ordner mit config erzeugen
    $path = $f3->get('ROOT').'\\formgen\\'.$dbname.'\\config';
    $chmod = 0777;
    if(!(is_dir($path) OR is_file($path) OR is_link($path) )) { 
        echo $path;
        mkdir ($path,$chmod,true); 
    } else {
        $valid=[];
        $valid[]= "Ordner schon vorhanden"; 
        $f3->set('validdb',$valid);
        $this->addDbForm(); 
        return false;
    } 
    $fp = fopen($path.'\\dbconfig.csv', 'w');
    fputcsv($fp, $data);
    fclose($fp);
	return true;
}
function update_db ($dbname) {    

}