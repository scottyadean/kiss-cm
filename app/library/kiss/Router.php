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
       
       $this->roles = array( 0 => array('guests'),
                             1 => array('guests','users'),
                             2 => array('guests','users','admins'));
     
       
       $this->add("error", "/error/(\w+)", array('error'=> 'guests'));
       
         
       //add your index route
       $this->add("index", "/", array('index'    => 'guests',
                                      'about-us' => 'guests'));
       
       $this->add("account", "/account/(\w+)", array('index'   => 'users',
                                                     'welcome' => 'guests'));
       
       
       $this->add("auth", "/auth", array('index' => 'guests',
                                         'join'  => 'guests',
                                         'login' => 'guests',
                                         'logout'=> 'guests'));
       
       //example of a route with a callback
       $this->add("async",  "/async/(\w+)", array('index'=>'guests'),  function($args){
        
            base\Headers::set(200, $args['format']);
            var_dump($args); 
        
        });

       //map routes
       $this->map()->route();

    }

}