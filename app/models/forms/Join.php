<?php
namespace models\forms;

use \depage\htmlform\htmlform as formBuilder;


class Join {
    
    public $form;
    
    public function __construct($url="/auth/join") {
         
         // add form fields
         $form = new formBuilder('join', array( 'submitURL'=>$url,
                                                'successURL'=>'/account/welcome',
                                                'method'=>'POST',
                                                 'label'=>'join'));
         
         $form->addText('username', array('label' => 'Username', 'required' => true));
         $form->addEmail('email', array('label' => 'Email address','required' => true));
         $form->addPassword('password', array('label' => 'Password', 'required' => true));
         
         $this->form = $form;
         
    }
    
    public function getForm() {   
        return $this->form;
    }
    
    
}