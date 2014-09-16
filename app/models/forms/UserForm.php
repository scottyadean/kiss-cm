<?php

namespace models\forms;

class UserForm extends \kiss\base\Form {
    
    public function __construct() {
    
        
    }
    
    public function render($model = '\models\activerecord\User', $table = 'users') {
       
        return $model::connection()->columns($table);
        
        
    }

    
}