<?php

namespace base; 

class Controller {
    
    public $args;
    public $view;
    public $params;
    public $layout;
    public $scope;
    public $action;
    public $controller;
    
    public $disable_layout;
    public $disable_view;
    
    
    public function Controller() {
       
    }

    public function getMvc() {
        
         return (object)get_object_vars($this);    
        
    } 
    
   
    public function setProps(array $values) {
        
        foreach( $values as $k=>$v ) {
            $this->$k = $v;
        }
        
        return true; 
    }
    
    
    public function _asJson(array $array) {
        
       return json_encode($array);
        
    }

    
}