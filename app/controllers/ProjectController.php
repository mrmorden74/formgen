<?php

class ProjectController extends Controller {
   function render(){

$user = 'root';
$pass = '';
$server = 'localhost';

$dbh = new PDO( "mysql:host=$server", $user, $pass );
$dbs = $dbh->query( 'SHOW DATABASES' );

while( ( $db = $dbs->fetchColumn( 0 ) ) !== false )
{
    $db_list[] = $db;
}
    $this->f3->set('db_list',$db_list);
		// $user = new User($this->db);
		$this->f3->set('name',$this->f3->get('SESSION.user'));

  		$db = new DbList($this->db);
        $db->all();
        for ($db->load(); !$db->dry(); $db->next()){
            $data[] = $db->cast();
        }
        $this->f3->set('projects',$data);
        // $columns = $projects->schema();
        if(!$db->dry()) {
            $valid=[];
            $valid[]= "No Project exist!"; 
        }

// $this->f3->set('result',$db->exec('SHOW TABLES'));

		// $user = $this->f3->get($username);
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','projectBase.html');
        echo $template->render('base.html');
        var_dump($db);    
        var_dump($data);    
    }   
}