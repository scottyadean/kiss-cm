<?php

class Index extends kiss\base\Controller { 
 
    /**
     * publc __construct
     * put init code here
    */
    public function __construct() {
 
    }
 
    /**
     * publc index Action
     * view @  /views/index/index.phtml
    */
    public function indexAction() {
       
       $this->layout = "_index";
       $this->view = 'index/test';
       
    }
    
    /**
     * publc aboutUs Action
     * Sample - camelCase aboutUs method will map to view about-us 
     * view @ /views/index/about-us.phtml
    */
    public function aboutUsAction() {
    
    }
    
}
