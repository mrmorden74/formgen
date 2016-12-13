<?php

class ProjectController extends Controller {
   function render(){

    $db = new DbList($this->db);
    $db->all();
    for ($db->load(); !$db->dry(); $db->next()){
        $data[] = $db->cast();
    }
    $this->f3->set('dataFromDb',$data);

    if(!$db->dry()) {
        $valid=[];
        $valid[]= "No Project exist!"; 
    }

        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','projectBase.html');
        echo $template->render('base.html');
        var_dump($db);    
        var_dump($data);    
    }   
}

/*
    // Überprüfen ob Datenbank existiert
    // Bei Bedarf Erstellen
    // Create database
    $sql = "CREATE DATABASE ".$data['dbname'];
    if ($conn->query($sql) === TRUE) {

    } else {
        $valid=[];
        $valid[]= "Error creating database: " . $conn->errno; 
        $this->f3->set('validdb',$valid);
        if ($conn->errno <> '1007') {
        echo 'test';
            $this->addDbForm(); 
            exit;
        }
    }
    $conn->close();        
    // Ordner mit config erzeugen
    $path = $this->f3->get('ROOT').'\\formgen\\'.$data['dbname'].'\\config';
    $chmod = 0777;
    if(!(is_dir($path) OR is_file($path) OR is_link($path) )) { 
        echo $path;
        mkdir ($path,$chmod,true); 
    } else {
        $valid=[];
        $valid[]= "Ordner schon vorhanden"; 
        $this->f3->set('validdb',$valid);
        $this->addDbForm(); 
        exit;
    } 
    $fp = fopen($path.'\\dbconfig.csv', 'w');
    fputcsv($fp, $data);
    fclose($fp);
    */