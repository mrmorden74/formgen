<?php

class Project extends DB\SQL\Mapper{
	private $database;
	public function __construct(DB\SQL $db,$database) {
	    parent::__construct($db,$database);
	}
	
	public function all() {
	    $this->load();
	    return $this->query;
	}

	public function getById($id) {
	    $this->load(array('id=?',$id));
	    return $this->query;
	}

    public function getByName($name) {
        $this->load(array('dbname=?', $name));
	    return $this->query;
    }

	public function add() {
	    $this->copyFrom('POST');
	    $this->save();
	}
	
	public function edit($id) {
	    $this->load(array('id=?',$id));
	    $this->copyFrom('POST');
	    $this->update();
	}
	
	public function delete($id) {
	    $this->load(array('id=?',$id));
	    $this->erase();
	}


}