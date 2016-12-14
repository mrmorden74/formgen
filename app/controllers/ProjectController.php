<?php

class ProjectController extends Controller {
   function render(){

    $srv = new SrvList($this->db);
    $srv->all();
    for ($srv->load(); !$srv->dry(); $srv->next()){
        $datasrv[] = $srv->cast();
    }

    foreach ($datasrv as $key => $value) {
        $srvname[$value['id']] = $value['server'];
        $this->f3->set('srvs',$srvname);
    }

    $db = new DbList($this->db);
    $db->all();
    $count = 0;
    for ($db->load(); !$db->dry(); $db->next()){
        $data[] = $db->cast();
        $data[$count]['srvname'] = $srvname[$data[$count]['srvlist_id']]; 
        $count ++;
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
    var_dump($srvname);
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
    function addPrjUSr() {
        $data = $this->f3->get('POST');
        $valid = Validate::is_valid($data, array(
            'srvlist_id' => 'required|alpha_numeric',
            'dbname' => 'required|alpha_numeric',
            'projectname' => 'required|alpha_numeric',
        ));

        if($valid === true) {
            // continue
        } else {
            $this->f3->set('validprj',$valid);
            $id['id'] = $data['id'];
            $this->addPrjForm($f3, $id); 
            exit;
        }

  		$user = new Project($this->db);
		$user->srvlist_id = $data['srvlist_id'];
		$user->dbname = $data['dbname'];
		$user->projectname = $data['projectname'];
		$user->active = 1;
		$user->save();
        $this->f3->reroute('/addPrj/'.$data['srvlist_id']);
    }
