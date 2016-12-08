<?php

class AdminController extends Controller {
    function addUserForm(){

		// $user = new User($this->db);
		 $this->f3->set('name',$this->f3->get('SESSION.user'));

		// $user = $this->f3->get($username);
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','admin.html');
        $this->f3->set('admin_tool','adminUser.html');
        echo $template->render('base.html');
    }
    function addDbForm(){

		// $user = new User($this->db);
		 $this->f3->set('name',$this->f3->get('SESSION.user'));

		// $user = $this->f3->get($username);
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','admin.html');
        $this->f3->set('admin_tool','adminDb.html');
        echo $template->render('base.html');
    }
function addUser() {

        $data = $this->f3->get('POST');
        $valid = Validate::is_valid($data, array(
            'username' => 'required|alpha_numeric',
            'password1' => 'required|max_len,100|min_len,6',
            'optionsUserType' => 'required|contains,admin user'
        ));

        if ($data['password1'] <> $data['password2']) {
            $valid=[];
            $valid[]= "The passwords don't match";            
        }

  		$user2 = new User($this->db);
        $user2->getByName($data['username']);
        if(!$user2->dry()) {
            $valid=[];
            $valid[]= "Username allready exists"; 
        }

        if($valid === true) {
            // continue
        } else {
            $this->f3->set('validusr',$valid);;
            $this->addUserForm(); 
            exit;
        }
        $username = $data['username'];
        $password = $data['password1'];
        $type = $data['optionsUserType'];


  		$user = new User($this->db);
		$user->username = $username;
		$user->password = password_hash($password, PASSWORD_DEFAULT);
		$user->type = $type;
		$user->save();
        $this->f3->reroute('/');
    }

function addDb() {

    $data = $this->f3->get('POST');
    $valid = Validate::is_valid($data, array(
        'server' => 'required|alpha_numeric',
        'username' => 'required|max_len,100|min_len,4',
        'dbpassword' => 'max_len,100',
        'dbtype' => 'required',
        'dbname' => 'required|alpha_numeric',
    ));

    $dbInDbList = new DbList($this->db);
    $dbInDbList->getByName($data['dbname']);
    if(!$dbInDbList->dry()) {
        $valid=[];
        $valid[]= "Database allready exists"; 
    }

    if($valid === true) {
        // continue
    } else {
        $this->f3->set('validdb',$valid);;
        $this->addDbForm(); 
        exit;
    }
    // Verbindung testen
    // Create connection
    $conn = new mysqli($data['server'], $data['username'], $data['password']);
    // Check connection
    if ($conn->connect_error) {
        $valid=[];
        $valid[]= "Connection failed: " . $conn->connect_error; 
        $this->f3->set('validdb',$valid);;
        $this->addDbForm(); 
        exit;
    } 
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
    
        // Datenbank in Datenbankliste eintragen
        $server = $data['server'];
        $dbname = $data['dbname'];
        $username = $data['username'];
        $password = $data['dbpassword'];
        $dbtype = $data['dbtype'];

  		$dbInDbList = new DbList($this->db);
		$dbInDbList->dbname = $dbname;
		$dbInDbList->server = $server;
		$dbInDbList->username = $username;
		$dbInDbList->password = $password; //Plaintext
		$dbInDbList->dbtype = $dbtype;
		$dbInDbList->save();
        $this->f3->reroute('/');
    }
    function init() {
        echo 'init';
    }
}