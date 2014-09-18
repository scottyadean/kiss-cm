<?php
namespace models\forms;
use \depage\htmlform\htmlform as formBuilder;

class Login {
    
    public $form;
    
    public function __construct($url="/auth/login") {
         // add form fields
         $form = new formBuilder('login', array('submitURL'=>$url,
                                                              'successURL'=>'/account/index',
                                                              'method'=>'POST',
                                                              'label'=>'login'));
         
         $form->addText('username', array('label' => 'User name', 'required' => true));
         $form->addPassword('password', array('label' => 'password', 'required' => true));
         $this->form = $form;        
    }
    
    public function getForm() {
        return $this->form->__toString();
    }
    
    public function getFields($model = '\models\activerecord\User', $table = 'users') {
        return UserModel::connection()->columns('users');
    }
}