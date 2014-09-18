<?php

//you can run static routes like this: 
       /*
        $this->get('/(\w+)', function($name) {
            echo "{action:$name}";
        });
	  $this->runStatic();
        */
       
namespace kiss;

  class Router extends base\Routes {
    
    public function __construct() {

       //create an instance of the template parsing class 
       $this->template = new base\Template();
       
       //Set default controller and action.
       $this->defaultAction  =  $this->defaultController = 'index';
       $this->errorAction    =  $this->errorController   = 'error';
       
       
       //set up acl for routes
       //Note is the route is missing from the acl it will assume guests can not access the resource
       $this->aclEnabled = array('controller'=>'auth', 'action'=>'login');
       
       $this->roles = array( 0 => array('guest'),
                             1 => array('guest','users'),
                             2 => array('guest','users','admin'));
       
       //add your index route
       $this->add("index", "/", array('index'    => 'guest',
                                      'about-us' => 'guest'));
       
     
       $this->add("error", "/error/(\w+)", array('error'=> 'guest'));
       
       
       $this->add("account", "/account/(\w+)", array('index' => 'users'));
       
       
       $this->add("auth", "/auth/(\w+)", array('index' => 'guest',
                                               'join'  => 'guest',
                                               'login' => 'guest',
                                               'logout'=> 'guest'));
       
       //example of a route with a callback
       $this->add("async",  "/async/(\w+)", array('index'=>'guest'),  function($args){
        
            base\Headers::set(200, $args['format']);
            var_dump($args); 
        
        });

       //map routes
       $this->map()->route();

    }

}