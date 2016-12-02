<?php

class DemoFormController extends Controller {
    function render(){

        $template=new Template;
        $this->f3->set('content','demoform.html');
        echo $template->render('base.html');
        
    }

}