<?php

namespace kiss;

  class Router extends base\Routes {
    

    public function __construct() {

       //create an instance of the template parsing class 
       $this->template = new Template();
       
       //Set default controller and action.
       $this->defaultAction  =  $this->defaultController = 'index';
       $this->errorAction    =  $this->errorController   = 'error';
       
       /**
        * you can run static routes like this: 
        * $this->get('/', function() { echo "static routes"});
	* $this->runStatic();     
       */
        
                    
       //add your active routes
       $this->add("index", "/", array('index','index-info'));
       $this->add("sync",  "/sync/(\w+)", array('index', 'info'));
            
       $this->map();
       $this->route();

 
    }
    
    public function index() {
       
       $this->_setHeader(200, 'html');
       return $this->template->mvc($this->defaultConfig());
    }

    public function sync() {
       $this->_setHeader(200, 'json');
       return $this->template->mvc($this->defaultConfig());
    }
    
  }