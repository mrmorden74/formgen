<?php
/**
*   Child class des Controller(f3)
*   Steuert User Anmeldeprozess
*/
class UserController extends Controller {
    function login(){

        $template=new Template;
        $this->f3->set('header','blank.html');
        $this->f3->set('content','login.html');
        echo $template->render('base.html');
        
    }
    function beforeroute() {

    }
    function authenticate(){
        $username = $this->f3->get('POST.username');
        $password = $this->f3->get('POST.password');

        $user = new User($this->db);
        $user->getByName($username);

        if($user->dry()) {
            $this->f3->reroute('/login');
        }

        if (password_verify($password,$user->password)) {
            $this->f3->set('SESSION.user',$user->username);
            $this->f3->set('SESSION.type',$user->type);
            $this->f3->reroute('/');
        } else {
            $this->f3->reroute('/login');
        }
    }
    function logout() {
            $this->f3->set('SESSION.user',NULL);
            $this->f3->reroute('/login');        
    }
    
}