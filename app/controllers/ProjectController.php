<?php

class ProjectController extends Controller {
   function render() {

    $srv = new SrvList($this->db);
    for ($srv->all(); !$srv->dry(); $srv->next()){
        $datasrv[] = $srv->cast();
    }
    if(count($datasrv)>0) {
        foreach ($datasrv as $key => $value) {
            $srvname[$value['id']] = $datasrv[$key];
            $this->f3->set('srvs',$srvname);
        }   
    }
    $db = new DbList($this->db);
    $count = 0;
    $data = [];
    
        for ($db->all(); !$db->dry(); $db->next()){
            $data[] = $db->cast();
            $data[$count]['server'] = $srvname[$data[$count]['srvlist_id']]['server']; 
            $data[$count]['username'] = $srvname[$data[$count]['srvlist_id']]['username']; 
            $data[$count]['password'] = $srvname[$data[$count]['srvlist_id']]['password']; 
            if ($conn = create_con (
                $data[$count]['server'], 
                $data[$count]['username'], 
                $data[$count]['password'])) {
            $data[$count]['tables'] = show_tables ($conn,$data[$count]['dbname'])->num_rows ?? 0;
            }
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
    
    function addPrjUsr() {
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
            $this->render(); 
            exit;
        }
        $srv = new SrvList($this->db);
        $srv->getById($data['srvlist_id']);
        if($srv->dry()) {
            $valid=[];
            $valid[]= "No login information stored"; 
            $this->render(); 
            exit;
        }
        if ($conn = create_con ($srv->server, $srv->username, $srv->password)) {
            // TODO NAchfragen wenn DB schon existiert. Verwenden o. Abbrechen
            if ($conn = create_db ($conn, $data['dbname'])) {
                $user = new Project($this->db);
                $user->srvlist_id = $data['srvlist_id'];
                $user->dbname = $data['dbname'];
                $user->projectname = $data['projectname'];
                $user->active = 1;
                $user->save();
                $conn = close_dbcon ($conn);
                $this->f3->reroute('/');
            }
        }
            $id['id'] = $data['id'];
            $this->render(); 
            exit;
    }
    
    function showForms ($f3,$params) {
        // echo $params['id'];
        $srv = new SrvList($this->db);
        $srv->getById($params['srvid']);
        if($srv->dry()) {
            $valid=[];
            $valid[]= "No login information stored"; 
            $this->f3->reroute('/showForms/'.$params['srvid'].'/'.$params['id']); 
            exit;
        }
        $db = new DbList($this->db);
        $db->getById($params['id']);
        if($db->dry()) {
            $valid=[];
            $valid[]= "No login information stored"; 
            $this->f3->reroute('/showForms/'.$params['srvid'].'/'.$params['id']);
            exit;
        }
    for ($srv; !$srv->dry(); $srv->next()){
        $datasrv[] = $srv->cast();
    }
    for ($db; !$db->dry(); $db->next()){
        $datadb[] = $db->cast();
    }
    
        $this->f3->set('srvdata',$datasrv);
        $this->f3->set('dbdata',$datadb);


        $conn = create_con ($datasrv[0]['server'], $datasrv[0]['username'], $datasrv[0]['password'], $datadb[0]['dbname']);
        $result = show_tables($conn);
        while ($row = $result->fetch_assoc()) {
            $tables[] = $row['Tables_in_'.$datadb[0]['dbname']];
        }
        $this->f3->set('dataFromDb',$tables);

        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','formBase.html');
        echo $template->render('base.html');
    var_dump($datadb);
    var_dump($datasrv);
    var_dump($tables);
        var_dump($data);    

    }
    function addFrmForm($f3,$id) {
        $db = new SrvList($this->db);
        $db->load(array('id=?',$id['id']));
        $db->copyTo('POST');
        if(!$db->dry()) {
            $valid=[];
            $valid[]= "No data exist!"; 
        }
        if($con = create_con($_POST['server'],$_POST['username'],$_POST['password'])) {
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
        var_dump($dbs3);
    }    
}