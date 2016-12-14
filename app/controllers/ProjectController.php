<?php

class ProjectController extends Controller {
   function render(){

    $srv = new SrvList($this->db);
    $srv->all();
    for ($srv->load(); !$srv->dry(); $srv->next()){
        $datasrv[] = $srv->cast();
    }
    foreach ($datasrv as $key => $value) {
        $srvname[$value['id']] = $datasrv[$key];
        $this->f3->set('srvs',$srvname);
    }

    $db = new DbList($this->db);
    $db->all();
    $count = 0;
    for ($db->load(); !$db->dry(); $db->next()){
        $data[] = $db->cast();
        $data[$count]['server'] = $srvname[$data[$count]['srvlist_id']]['server']; 
        $data[$count]['username'] = $srvname[$data[$count]['srvlist_id']]['username']; 
        $data[$count]['password'] = $srvname[$data[$count]['srvlist_id']]['password']; 
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

}