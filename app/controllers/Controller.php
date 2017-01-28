<?php
/**
*	Base Controller von fatfree
*	Instantiert das f3 Objekt und steuert fixe Abläufe vor und nach allen Routings 
*/
class Controller {
    
    protected $f3;
    protected $db;

	function initiate($init) {
		if($this->f3->get('devdb') === null ){
			$filename = $init;

			if (file_exists($filename)) {
				echo "Die Datei $filename existiert";
			} else {
				echo "Die Datei $filename existiert nicht";
				$this->f3->reroute('/init');
				exit;
			}

		}
	}

    function beforeroute() {
        // echo 'Before routing - ';
		if($this->f3->get('SESSION.user') === null ){
			$this->f3->reroute('/login');
			exit;
		}
    }
    function afterroute() {
        // echo ' - After routing';
		if ($this->f3->get('DEBUG')>0) {
			var_dump($this->f3);
		}
    }

	function __construct() {
		
		$f3=Base::instance();
		$this->f3=$f3;

	    $db=new DB\SQL(
	        $f3->get('devdb'),
	        $f3->get('devdbusername'),
	        $f3->get('devdbpassword'),
	        array( \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION )
	    );

	    $this->db=$db;
	}    

}