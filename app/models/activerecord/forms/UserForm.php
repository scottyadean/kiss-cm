<?php

namespace models\activerecord\forms;
use \kiss\base\activerecord\Form as activeRecordForm;
use \models\activerecord\User as UserModel;

class UserForm extends activeRecordForm {
    
    public function __construct() {
        
    }
    
    public function render($model = '\models\activerecord\User', $table = 'users') {
       
        
        return UserModel::connection()->columns('users');
        
        
    }

    
}