<?php

  class Routes extends base\Routes {
    

    public function __construct() {
        
       //create an instance of the template parsing class 
       $this->template = new Template();
       
       //Set default controller and action.
       $this->defaultController = 'index';
       $this->defaultAction     = 'index';
       
       //add your active routes
       $this->routes  = array('index' => array('actions'=>array('index', 'index-info')),
                              'sync'  => array('actions'=>array('index', 'info')));
    
    }
    
    
    public function index() {
       
       $this->_setHeader(200, 'html');
       $this->title = "Welcome";
       return $this->template->parseController($this->defaultConfig());
    
    }

    public function sync() {
       
       $this->_setHeader(200, 'json');
       return $this->template->parseController($this->defaultConfig());
    }
        
    
  }
