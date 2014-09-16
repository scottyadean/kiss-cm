<?php

use models\forms as forms;
use models\activerecord as models;

class Index extends kiss\base\Controller { 
 
    /**
     * publc __construct
     * put init code here
    */
    public function __construct() {
 
    }
 
    /**
     * publc index Action
     * view @  /views/index/index.phtml
    */
    public function indexAction() {
       
        $user = new models\User();
        $form = new forms\UserForm();
        
        $this->form = $fields = $form->render();
        
        
        foreach($fields as $f) {
            
            print $f->name. " : ". $f->raw_type. " ( " .$f->length. " )<br />";
            
        }
        
        

    }
    
    /**
     * publc aboutUs Action
     * Sample - camelCase aboutUs method will map to view about-us 
     * view @ /views/index/about-us.phtml
    */
    public function aboutUsAction() {
    
       //you can swop the layout and view in the controller..
       $this->layout = "_index";
       $this->view = 'index/test';
    
    }
    
}
