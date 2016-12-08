<?php
    // Verbindung testen
    // Create connection
function create_con ($server, $user, $pwd) {
    $conn = new mysqli($server, $user, $pwd);
    // Check connection
    if ($conn->connect_error) {
        $valid=[];
        $valid[]= "Connection failed: " . $conn->connect_error; 
        $f3->set('validdb',$valid);;
        $this->addDbForm(); 
        return false;
    }
	return $conn;
 } 
 
    // Überprüfen ob Datenbank existiert
    // Bei Bedarf Erstellen
    // Create database
function create_db ($conn, $dbname) {
    $sql = "CREATE DATABASE ".$dbname;
    if ($conn->query($sql) === TRUE) {

    } else {
        $valid=[];
        $valid[]= "Error creating database: " . $conn->errno; 
        $this->f3->set('validdb',$valid);
        if ($conn->errno <> '1007') {
            $this->addDbForm(); 
            return false;
        }
    }
    $conn->close();   
	return true;  
}   

function create_folder ($dbname) {
    // Ordner mit config erzeugen
    $path = $this->f3->get('ROOT').'\\formgen\\'.$dbname.'\\config';
    $chmod = 0777;
    if(!(is_dir($path) OR is_file($path) OR is_link($path) )) { 
        echo $path;
        mkdir ($path,$chmod,true); 
    } else {
        $valid=[];
        $valid[]= "Ordner schon vorhanden"; 
        $this->f3->set('validdb',$valid);
        $this->addDbForm(); 
        return false;
    } 
    $fp = fopen($path.'\\dbconfig.csv', 'w');
    fputcsv($fp, $data);
    fclose($fp);
	return true;
}
    
