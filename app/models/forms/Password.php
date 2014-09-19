<?php
namespace models\forms;

use \depage\htmlform\htmlform as formBuilder;


class Password {
    
    public $form;
    
    public function __construct($url="/auth/pass") {
         
         // add form fields
         $form = new formBuilder('recoverpass', array( 'submitURL'=>$url,
                                                       'successURL'=>$url.'/message/1/',
                                                        'method'=>'POST',
                                                        'label'=>'send'));
         
         $form->addText('username', array('label' => 'Username', 'required' => true));
         
         $this->form = $form;
         
    }
    
    public function getForm() {   
        return $this->form;
    }
    
    
}