<?php

class MainController extends Controller {
    function render(){

		// $user = new User($this->db);
		// $user->username = 'admin';
		// $user->password = password_hash("admin", PASSWORD_DEFAULT);
		// $user->save();

		// $user = new User($this->db);
		 $this->f3->set('name',$this->f3->get('SESSION.user'));

		// $user = $this->f3->get($username);
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','admin.html');
        echo $template->render('base.html');
    }

}