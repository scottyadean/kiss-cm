<?php
namespace models\forms;

use \depage\htmlform\htmlform as formBuilder;


class Join {
    
    public $form;
    
    public function __construct($url="/auth/join") {
         
         // add form fields
         //find examples here:
         //http://docs.depage.net/depage-forms/documentation/html/examples.html
         $form = new formBuilder('join', array( 'submitURL'=>$url,
                                                'successURL'=>'/account/welcome',
                                                'method'=>'POST',
                                                 'label'=>'join'));
         
         $form->addText('username', array('label' => 'Username', 'required' => true,
                                          'validator' => '/.{6,}/', 'title' => 'at least 3 characters'));
         
         $form->addEmail('email', array('label' => 'Email address','required' => true));
         $form->addPassword('password', array('label' => 'Password','id'=>'passinput',
                                              'required' => true,
                                              'autocomplete' => false,
                                              'validator' => '/(?=^.{8,}$)(?=.*\d)(?=.*[!@#$%^&*]+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/'));
         
       
         $this->form = $form;
         
    }
    
    public function getForm() {   
        return $this->form;
    }
    
    
}