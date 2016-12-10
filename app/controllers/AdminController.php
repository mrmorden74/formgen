<?php

class AdminController extends Controller {
    /**
    *  Aufruf des Formulars zur Benutzereingabe     
    *  param $f3	object	fatfree object
    *  param $id	array	id in der Form von f3
    *  return rtype	rdescription
    */    
    function editUserForm($f3,$id) {
        $db = new User($this->db);
        $db->load(array('id=?',$id['id']));
        $db->copyTo('POST');
        if(!$db->dry()) {
            $valid=[];
            $valid[]= "No data exist!"; 
        }

        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','admin.html');
        $this->f3->set('admin_tool','adminUserEdit.html');
        echo $template->render('base.html');
        var_dump($db);
 
    }
    function editSrvForm($f3,$id) {
        $db = new SrvList($this->db);
        $db->load(array('id=?',$id['id']));
        $db->copyTo('POST');
        if(!$db->dry()) {
            $valid=[];
            $valid[]= "No data exist!"; 
        }

        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','admin.html');
        $this->f3->set('admin_tool','adminSrvEdit.html');
        echo $template->render('base.html');
        var_dump($db);
 
    }    
    function editUser($f3,$id_array) {

        $data = $this->f3->get('POST');
        var_dump($data);
        $valid = Validate::is_valid($data, array(
            'username' => 'required|alpha_numeric',
            'type' => 'required|contains,admin user'
        ));

        if ($data['password'] <> $data['password2']) {
            $valid=[];
            $valid[]= "The passwords don't match";            
        }

        if($valid === true) {
            // continue
        } else {
            $this->f3->set('validusr',$valid);;
            $this->editUserForm($f3,$id); 
            exit;
        }
  		$user = new User($this->db);
		$user->edit($id_array['id']);
        $this->f3->reroute('/showUser');
    }
    function delUser($f3,$id) {
        $db = new User($this->db);
        $db->delete($id['id']);
        $this->f3->reroute('/showUser');
    }
    function showUser(){
        $db = new User($this->db);
        $db->all();
        for ($db->load(); !$db->dry(); $db->next()){
            $data[] = $db->cast();
        }
        $this->f3->set('dataFromDb',$data);
        // $columns = $projects->schema();
        if(!$db->dry()) {
            $valid=[];
            $valid[]= "No data exist!"; 
        }
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','userBase.html');
        echo $template->render('base.html');
        // var_dump($db);    
        // var_dump($data);    
    }
    function showSrv(){
        $db = new SrvList($this->db);
        $db->all();
        for ($db->load(); !$db->dry(); $db->next()){
            $data[] = $db->cast();
        }
        $this->f3->set('dataFromDb',$data);
        // $columns = $projects->schema();
        if(!$db->dry()) {
            $valid=[];
            $valid[]= "No data exist!"; 
        }
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','srvBase.html');
        echo $template->render('base.html');
        // var_dump($db);    
        // var_dump($data);    
    }
    
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
    function addSrvForm(){

		// $user = new User($this->db);
		 $this->f3->set('name',$this->f3->get('SESSION.user'));

		// $user = $this->f3->get($username);
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','admin.html');
        $this->f3->set('admin_tool','adminSrv.html');
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
        $this->f3->reroute('/showUser');
    }

function addSrv() {

    $data = $this->f3->get('POST');
    $valid = Validate::is_valid($data, array(
        'server' => 'required|alpha_numeric',
        'srvtype' => 'required',
        'username' => 'required|max_len,100|min_len,4',
        'password' => 'max_len,100',
    ));

    $dbInDbList = new SrvList($this->db);
    $dbInDbList->getByName($data['server']);
    if(!$dbInDbList->dry()) {
        $valid=[];
        $valid[]= "Database allready exists"; 
    }

    if($valid === true) {
        // continue
    } else {
        $this->f3->set('validdb',$valid);;
        $this->addSrvForm(); 
        exit;
    }
    // Verbindung testen
    // Create connection
    $conn = create_con ($data['server'], $data['username'], $data['password']);
    // Check connection
    if ($conn->connect_error) {
        $valid=[];
        $valid[]= "Connection failed: " . $conn->connect_error; 
        $this->f3->set('validdb',$valid);;
        $this->addSrvForm(); 
        exit;
    } 
    
        // Datenbank in Datenbankliste eintragen
        $server = $data['server'];
        $srvtype = $data['srvtype'];
        $username = $data['username'];
        $password = $data['password'];

  		$dbInDbList = new SrvList($this->db);
		$dbInDbList->server = $server;
		$dbInDbList->srvtype = $srvtype;
		$dbInDbList->username = $username;
		$dbInDbList->password = $password; //Plaintext
		$dbInDbList->save();
        $this->f3->reroute('/showSrv');
    }
    function init() {
        echo 'init';
    }
}