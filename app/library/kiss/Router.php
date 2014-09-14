<?php

namespace kiss;

  class Router extends base\Routes {
    

    public function __construct() {

       //create an instance of the template parsing class 
       $this->template = new Template();
       
       //Set default controller and action.
       $this->defaultAction  =  $this->defaultController = 'index';
       $this->errorAction    =  $this->errorController   = 'error';
       /*
        //you can run static routes like this: 
        $this->get('/(\w+)', function($name) {
            echo "{action:$name}";
        });
	 $this->runStatic();
        
        */
        
       //add your active routes
       $this->add("index", "/", array('index', 'about-us'));
       
       //add a rout with a callback
       $this->add("async",  "/async/(\w+)", array('index'), function($args) {
            echo "{action:$args}";
       });

       //map routes
       $this->map()->route();

    }

  }