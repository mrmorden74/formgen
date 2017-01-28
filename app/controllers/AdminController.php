<?php
/**
*   Child class des Controller(f3)
*   Steuert alle Admintools
*/
class AdminController extends Controller {
    /**
    *  Aufruf des Formulars zur Benutzereingabe     
    *  param $f3	object	fatfree object
    *  param $id['id']	array	UserId
    *  return render adminUserEdit.html
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
        //DEBUG
        if ($this->f3->get('DEBUG')>0) {
            // var_dump($db);
        }
    }

    /**
    *  Rendert den ausgewählten Server (Host) zur Bearbeitung 
    *  param $f3	object	fatfree object
    *  param $id['id']	array	ServerId
    *  return render adminSrvEdit.html
    */  
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
        //DEBUG
        if ($this->f3->get('DEBUG')>0) {
            // var_dump($db);
        }
    }    

    /**
    *  Rendert Formular zum Erstellen eines neuen Projekts (Datenbank)
    *  Datenbanken am Host werden vorgegeben 
    *  param $f3	object	fatfree object
    *  param $id['id']	array	ServerId
    *  return render adminPrjAdd.html
    */  
    function addPrjForm($f3,$id) {
        $db = new SrvList($this->db);
        $db->load(array('id=?',$id['id']));
        $db->copyTo('POST');
        if(!$db->dry()) {
            $valid=[];
            $valid[]= "No data exist!"; 
        }

        $blacklist = [
            "performance_schema",
            "secure_login",
            "sys"
        ];
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
                        if (!in_array($dbname, $blacklist)) {
                        $dbs3[$dbname] = $_POST;
                            if ($dbs3[$dbname]['password']) {
                                $dbs3[$dbname]['password'] = $this->decrypt($dbs3[$dbname]['password']);
                            }
                        }
                    }
                }
                // var_dump($dbs3);
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

    /**
    *  Validiert und speichert geänderte Userdaten in der Datenbanktabelle user
    *  param $f3    object	fatfree Object
    *  param $id['id']	array	UserId
    *  return reroute showUser
    */
    function editUser($f3,$id) {
        $data = $this->f3->get('POST');
        // var_dump($data);
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
		$user->edit($id['id']);
        // var_dump($user);
        $this->f3->reroute('/showUser');
    }

    /**
    *  Löscht Usereintrag aus der Datenbanktabelle user
    *  param $f3    object	fatfree Object
    *  param $id['id']	array	UserId
    *  return reroute showUser
    */
    function delUser($f3,$id) {
        $usr = new User($this->db);
        $usr->delete($id['id']);
        $this->f3->reroute('/showUser');
    }

    /**
    *  Löscht Servereintrag aus der Datenbanktabelle srvlist
    *  Prüft zuvor auf Einträge in der dblist.
    *  param $f3    object	fatfree Object
    *  param $id['id']	array	ServerID
    *  return route showSrv
    */
    function delSrv($f3,$id) {
        $srv = new SrvList($this->db);
        $db = new DbList($this->db);
		for ($db->load(array('srvlist_id=?',$id['id'])); !$db->dry(); $db->next()){
            $datadb[] = $db->cast();
        } 
        // var_dump($datadb); 
        if(count($datadb)>0) {
            $valid[] = "Auf diesem Server wurden schon Projekte eingerichtet.\nLöschen sie diese zuerst";
            $this->f3->set('validsrv',$valid);
        } else {        
        $srv->delete($id['id']);
        }
        // $this->f3->reroute('/showSrv');
        $this->showSrv();
    }

    /**
    *  Löscht Proejkteintrag aus der Datenbanktabelle DbList
    *  Prüft zuvor auf Einträge in der TblList.
    *  param $f3    object	fatfree Object
    *  param $id['id']	array	DatenbankId
    *  return route addPrj
    */
    function delPrj($f3,$id) {
        $db = new DbList($this->db);

  		$frm = new TblList($this->db);
		for ($frm->load(array('dbid=?',$id['id'])); !$frm->dry(); $frm->next()){
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
            // echo 'TEST';
            $db->delete($id['id']);
        $this->f3->reroute('/addPrj/'.$id['srvid']);
        }
    }

    /**
    *  Rendert Userliste
    *  return render adminUserBase
    */
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

    /**
    *  Rendert Server (Hosts)
    *  return render adminSrvBase
    */
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
        //DEBUG
        if ($this->f3->get('DEBUG')>0) {
            // var_dump($db);    
        }
    }
    
    /**
    *  Rendert Formular zur Usereingae
    *  return render adminUserAdd.html
    */
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

    /**
    *  Rendert Formular zur Servereingabe
    *  return render adminSrvAdd.html
    */
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

    /**
    *  Validiert und fügt Projekt (Datenbank) aus $_POST 
    *  in die SrvList hinzu.
    *  return reroute addPrj
    */
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
  		// $pw = $this->f3->hash($data['password']);
            // var_dump ($data);
        $user = new DbList($this->db);
		$user->srvlist_id = $data['srvlist_id'];
		$user->dbname = $data['dbname'];
		$user->projectname = $data['projectname'];
		$user->username = $data['username'];
        if (strlen($data['password'])) {
        $user->password = $this->encrypt($data['password']);
        }
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

    /**
    *  Validiert und fügt User aus $_POST in die User Tabelle hinzu.
    *  return reroute showUser
    */
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

    /**
    *  Validiert und fügt Server aus $_POST in die SrvList hinzu
    *  return route addSrvForm
    */
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
            $this->f3->set('validsrv',$valid);;
            $this->addSrvForm(); 
            exit;
        }
        // Verbindung testen
        // Create connection
        if (!$conn = create_con ($data['server'], $data['username'], $data['password'])) {
            $valid2[]= "Connection failed!"; 
            $this->f3->set('validsrv',$valid2);
            $this->addSrvForm(); 
            exit;
        }
        // Check connection
        if ($conn->connect_error) {
            $valid2=[];
            $valid2[]= "Connection failed: " . $conn->connect_error; 
            $this->f3->set('validsrv',$valid2);
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
            if (strlen($password)) {
            $dbInDbList->password = $this->encrypt($password); //Plaintext
            }
            $dbInDbList->save();
            $this->f3->reroute('/showSrv');
    }

    /**
    *  Validiert und speichert geänderte Serverdaten.
    *  param $f3    object	fatfree Object
    *  param $id['id']	array	ServerID
    *  return reroute showSrv
    */
    function editSrv($f3,$id) {
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
            $this->editSrvForm($f3,$id);
            exit;
        }
        $srv = new SrvList($this->db);
		$srv->server = $data['server'];
		$srv->srvtype = $data['srvtype'];
		$srv->username = $data['username'];
        if (strlen($_POST['password'])) {
            $_POST['password'] = $this->encrypt($data['password']);
		$srv->password = $pwcrypt;
            }
		$srv->edit($id['id']);
        $this->f3->reroute('/showSrv');
    }

    /**
    *  Rendert das ausgewählte Projekt zur Bearbeitung 
    *  param $f3	object	fatfree object
    *  param $id['id']	array	ServerId
    *  return render adminSrvEdit.html
    */  
    function editPrjForm($f3,$id) {
        $db = new DbList($this->db);
        $db->load(array('id=?',$id['id']));
        $db->copyTo('POST');
        // var_dump($_POST);
        $_POST['password'] = $this->decrypt($_POST['password']);
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','admin.html');
        $this->f3->set('admin_tool','adminPrjEdit.html');
        echo $template->render('base.html');
        //DEBUG
        if ($this->f3->get('DEBUG')>0) {
            // var_dump($db);
        }
    }

    /** 
    *  TODO:  Edit Projekt
    *  Validiert und speichert geänderte Projektdaten.
    *  return route showSrv
    */
    function editPrj($f3,$id) {
        $data = $this->f3->get('POST');
        $valid = Validate::is_valid($data, array(
            'dbname' => 'required|alpha_numeric',
            'projectname' => 'required|alpha_numeric',
        ));
        $this->f3->POST['password'] = $this->encrypt($this->f3->POST['password']);

        if($valid === true) {
            // continue
        } else {
            $this->f3->set('validprj',$valid);;
            $this->editPrjForm($f3,$id);
            exit;
        }
        $db = new DbList($this->db);
		$db->dbname = $data['dbname'];
		$db->projectname = $data['projectname'];
		$db->username = $data['username'];
        if (strlen($_POST['password'])) {
            $_POST['password'] = $this->encrypt($data['password']);
		$db->password = $_POST['password'];
            }
		$db->edit($id['id']);
        $srvId = $this->getSrvIDfromDbId ($id['id']);
        //dbconfig exportieren
        $path = $this->f3->get('ROOT');
        $srv = new SrvList($this->db);
            for ($srv->getById($srvId); !$srv->dry(); $srv->next()){
                $datasrv[] = $srv->cast();
            }
            $datasrv[0]['dbname'] = $data['dbname'];
            $datasrv[0]['projectname'] = $data['projectname'];
            $datasrv[0]['dblist_id'] = $data['id'];

        $create = create_folder($path, $datasrv[0]);

        $this->f3->reroute('/addPrj/'.$srvId);
    }

/**
*  Liefer die passende ServerId zur DbListId
*  param $DbId	int	Datenbank = Projekt Id
*  return Int	ServerId
*/
    function getSrvIDfromDbId ($DbId) {
        $db = new DbList($this->db);
		for ($db->load(array('id=?',$DbId)); !$db->dry(); $db->next()){
            $datadb[] = $db->cast();
        }
        return $datadb[0]['srvlist_id'];
    }

    /**
    *  Verschlüsselt $token mit vorgebenen ENCRYPTION_KEY.
    *  twoway encryption
    *  param    $token  string  Zu verschlüsselnder String
    *  return   string  verschlüsselter Token
    */
    function encrypt($token) {
        $cryptor = new Cryptor($this->f3->get('ENCRYPTION_KEY'));
        return $cryptor->encrypt($token);
    }

    /**
    *  Entschlüsselt $token mit vorgebenen ENCRYPTION_KEY.
    *  twoway encryption
    *  param    $token  string  verschlüsselter String
    *  return   string  entschlüsselter Token
    */
    function decrypt($crypted_token) {
        $cryptor = new Cryptor($this->f3->get('ENCRYPTION_KEY'));
        // echo $encryption_key;
        return $cryptor->decrypt($crypted_token);
    }

}