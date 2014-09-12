<?php

class Index extends kiss\base\Controller { 
 
    
    public function __construct() {
 
    }
 
 
    public function indexAction(){
        

    }
    
    
    public function indexInfoAction() {
        
        
        
    }
    
    
    public function infoAction() {
        
        
      print  $this->_asJson(array("TEST"=>"Stuff"));
        
        
    }
    
    
   
 }
