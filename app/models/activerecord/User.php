<?php

namespace models\activerecord;

class User extends \ActiveRecord\Model{
        
    public function _create($data) {
        return self::create($this->clean($data));
        
    }
 
 
    public function clean($data) {
        return $data;
    }   
    
}