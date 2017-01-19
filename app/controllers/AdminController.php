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
        $_POST['password'] = $this->decrypt($_POST['password']);        if(!$db->dry()) {
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

    function addPrjForm($f3,$id) {
        $db = new SrvList($this->db);
        $db->load(array('id=?',$id['id']));
        $db->copyTo('POST');
        if(!$db->dry()) {
            $valid=[];
            $valid[]= "No data exist!"; 
        }
        $password =  $this->decrypt($_POST['password']);
        if($con = create_con($_POST['server'],$_POST['username'],$password)) {
            if($result = show_db($con)) {
                while( $row = mysqli_fetch_row( $result ) ){
                    if (($row[0]!="information_schema") && ($row[0]!="mysql")) {
                        $dbname = $row[0];
                        $dbs[$dbname]['dbname'] = $dbname;
                        $prjs = new DbList($this->db);
                        $prjs->load(array('dbname=?',$dbname));
                        $prjs->copyTo('POST');
                        $dbs3[$dbname] = $_POST;
                    }
                }
        $this->f3->set('srv_id',$id['id']);
            }
        }
        $this->f3->set('dataFromDb',$dbs3);
        $template=new Template;
        $this->f3->set('header','header.html');
        // $this->f3->set('content','adminPrjAdd.html');
        $this->f3->set('content','admin.html');
        $this->f3->set('admin_tool','adminPrjAdd.html');
        echo $template->render('base.html');
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
		$user->username = $data['username'];
		$user->password = password_hash($data['password'], PASSWORD_DEFAULT);
		$user->type = $data['type'];
		$user->edit($id_array['id']);
        // var_dump($user);
        $this->f3->reroute('/showUser');
    }

    function delUser($f3,$id) {
        $db = new User($this->db);
        $db->delete($id['id']);
        $this->f3->reroute('/showUser');
    }
    function delSrv($f3,$id) {
        $db = new SrvList($this->db);
        $db->delete($id['id']);
        $this->f3->reroute('/showSrv');
    }
    function delPrj($f3,$params) {
        $db = new DbList($this->db);

  		$frm = new TblList($this->db);
		for ($frm->load(array('dbid=?',$params['id'])); !$frm->dry(); $frm->next()){
            $datafrm[] = $frm->cast();
        }   
        if (count($datafrm)) {
            echo "<script type='text/javascript' language='javascript'>\n";
            echo "box = confirm('Es wurden bereist Formulare zu diesem Projekt ertstellt. Löschen sie diese Bevor sie das Projekt löschen')";
            // echo "if (box == false) { ";
            // echo "test";
            // echo " }";
            echo "</script>\n";
        // TODO Datenbankeinträge auch löschen
        } else {
            $db->delete($params['id']);
        $this->f3->reroute('/addPrj/'.$params['srvid']);
        }
    }

    function showUser(){
        $db = new User($this->db);
        $db->all();
        for ($db->load(); !$db->dry(); $db->next()){
            $data[] = $db->cast();
        }
        $this->f3->set('dataFromDb',$data);
        if(!$db->dry()) {
            $valid=[];
            $valid[]= "No data exist!"; 
        }
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','admin.html');
        $this->f3->set('admin_tool','adminUserBase.html');
        echo $template->render('base.html');
    }

    function showSrv(){
        $db = new SrvList($this->db);
        $db->all();
        $contbd = 0;
        for ($db->load(); !$db->dry(); $db->next()){
            $data[] = $db->cast();
        // var_dump($data[$contbd]);    
        $data[$contbd]['password'] = $this->decrypt($data[$contbd]['password']);
        $contbd ++;
        }
        $this->f3->set('dataFromDb',$data);
        if(!$db->dry()) {
            $valid=[];
            $valid[]= "No data exist!"; 
        }
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','admin.html');
        $this->f3->set('admin_tool','adminSrvBase.html');
        echo $template->render('base.html');
        // var_dump($db);    
    }
    
    function addUserForm(){

		// $user = new User($this->db);
		 $this->f3->set('name',$this->f3->get('SESSION.user'));

		// $user = $this->f3->get($username);
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','admin.html');
        $this->f3->set('admin_tool','adminUserAdd.html');
        echo $template->render('base.html');
    }
    function addSrvForm(){

		// $user = new User($this->db);
		 $this->f3->set('name',$this->f3->get('SESSION.user'));

		// $user = $this->f3->get($username);
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','admin.html');
        $this->f3->set('admin_tool','adminSrvAdd.html');
        echo $template->render('base.html');
    }

function addPrj() {
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
            $id['id'] = $data['srvlist_id'];
            $this->addPrjForm($f3, $id); 
            exit;
        }
  		$pw = $this->f3->hash($data['password']);
            var_dump ($data);
            var_dump ($pw);
        $user = new DbList($this->db);
		$user->srvlist_id = $data['srvlist_id'];
		$user->dbname = $data['dbname'];
		$user->projectname = $data['projectname'];
		$user->username = $data['username'];
        $user->password = $this->encrypt($data['password']);
		$user->active = 1;
		$user->save();
        $user->load(array('projectname=?',$data['projectname']));
        for ($user->load(array('projectname=?',$data['projectname'])); !$user->dry(); $user->next()){
            $datanew[] = $user->cast();
        }   
            // var_dump ($data2);
        $path = $this->f3->get('ROOT');
        $srv = new SrvList($this->db);
            for ($srv->getById($data['srvlist_id']); !$srv->dry(); $srv->next()){
                $datasrv[] = $srv->cast();
            }
            $datasrv[0]['dbname'] = $datanew[0]['dbname'];
            $datasrv[0]['projectname'] = $datanew[0]['projectname'];
            $datasrv[0]['dblist_id'] = $datanew[0]['id'];

            // var_dump ($data2);
            // var_dump ($datasrv);
        $create = create_folder($path, $datasrv[0]);
        $this->f3->reroute('/addPrj/'.$data['srvlist_id']);
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
        'server' => 'required',
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
		$dbInDbList->password = $this->encrypt($password); //Plaintext
		$dbInDbList->save();
        $this->f3->reroute('/showSrv');
    }
    function init() {
        echo 'init';
    }

    function editSrv($f3,$id_array) {
        $data = $this->f3->get('POST');
        $valid = Validate::is_valid($data, array(
            'server' => 'required',
            'srvtype' => 'required',
            'username' => 'required|max_len,100|min_len,4',
            'password' => 'max_len,100',
        ));

        if($valid === true) {
            // continue
        } else {
            $this->f3->set('validsrv',$valid);;
            $this->editSrvForm($f3,$id_array);
            exit;
        }
        $srv = new SrvList($this->db);
		$srv->server = $data['server'];
		$srv->srvtype = $data['srvtype'];
		$srv->username = $data['username'];
        $_POST['password'] = $this->encrypt($data['password']);
		$srv->password = $pwcrypt;
		$srv->edit($id_array['id']);
        $this->f3->reroute('/showSrv');
    }
    function editPrj() {
    $data = $this->f3->get('POST');
    // var_dump($data);
    // var_dump($this->f3->POST['password']);


    $crypted_token = $this->encrypt($this->f3->POST['password']);
    $decrypted_token = $this->decrypt($crypted_token);
 



    echo $crypted_token;
    echo $this->f3->get('ENCRYPTION_KEY');
    echo "-".$decrypted_token."-";
    }

    function encrypt($token) {
        $encryption_key = $this->f3->get('ENCRYPTION_KEY');
        $cryptor = new Cryptor($this->f3->get('ENCRYPTION_KEY'));
        return $cryptor->encrypt($token);
    }
    function decrypt($crypted_token) {
        $cryptor = new Cryptor($this->f3->get('ENCRYPTION_KEY'));
        return $cryptor->decrypt($crypted_token);
    }

}