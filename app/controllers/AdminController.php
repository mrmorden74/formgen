<?php

class AdminController extends Controller {
    function render(){

		// $user = new User($this->db);
		 $this->f3->set('name',$this->f3->get('SESSION.user'));

		// $user = $this->f3->get($username);
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','admin.html');
        echo $template->render('base.html');
    }
function addUser() {

        $data = $this->f3->get('POST');
        $valid = Validate::is_valid($data, array(
            'username' => 'required|alpha_numeric',
            'password1' => 'required|max_len,100|min_len,6'
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
            $this->render(); 
            exit;
        }
        $username = $data['username'];
        $password = $data['password1'];


  		$user = new User($this->db);
		$user->username = $username;
		$user->password = password_hash($password, PASSWORD_DEFAULT);
		$user->save();
        $this->f3->reroute('/');
    }

function addDb() {

        $data = $this->f3->get('POST');
        $valid = Validate::is_valid($data, array(
            'dbname' => 'required|alpha_numeric',
            'username' => 'required|max_len,100|min_len,4',
            'password' => 'max_len,100|min_len,5',
            'dbtype' => 'required'
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
            $this->render(); 
            exit;
        }
        // Verbindung testen
$servername = "localhost";
$username = $data['username'];
$password = $data['password'];

// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Create database
$sql = "CREATE DATABASE ".$data['dbname'];
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully";
} else {
    echo "Error creating database: " . $conn->error;
}

$conn->close();        
        // Überprüfen ob Datenbank existiert
        // Bei Bedarf Erstellen
        // Ordner mit config erzeugen
        // Datenbank in Datenbankliste eintragen

        $dbname = $data['dbname'];
        $username = $data['username'];
        $password = $data['password'];
        $dbtype = $data['dbtype'];

  		$dbInDbList = new DbList($this->db);
		$dbInDbList->dbname = $dbname;
		$dbInDbList->username = $username;
		$dbInDbList->password = $password; //Plaintext
		$dbInDbList->dbtype = $dbtype;
		// $dbInDbList->save();
        $this->f3->reroute('/');
    }

}