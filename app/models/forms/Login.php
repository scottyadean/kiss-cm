<?php


namespace models\forms;

class Login {
    
    
    
    public $form;
    
    public function __construct($url="/auth/login") {
         // add form fields
         $form = new \depage\htmlform\htmlform('login', array('submitURL'=>$url, 'successURL'=>'/account/index', 'method'=>'POST', 'label'=>'login'));
         
         $form->addText('username', array('label' => 'User name', 'required' => true));
         $form->addPassword('password', array('label' => 'password', 'required' => true));
         
         $this->form = $form;
         
       
        
    }
    
    
    public function getHtml() {
        
        return $this->form->__toString();
        
        
    }
    
    
}