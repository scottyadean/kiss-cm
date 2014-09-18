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
 
       //Set default controller, action, template.      
       $this->init();
       
       //error route
       $this->add("error", "/error/(\w+)", array('error'=> 'guests'));
         
       //add your index route
       $this->add("index", "/", array('index'    => 'guests',
                                      'about-us' => 'guests'));
       
       $this->add("account", "/account/(\w+)", array('index'   => 'users',
                                                     'welcome' => 'guests'));
       
       $this->add("my", "/my/(\w+)", array('index' => 'users'), "account@index");
        
       $this->add("auth", "/auth", array('index' => 'guests',
                                         'join'  => 'guests',
                                         'login' => 'guests',
                                         'logout'=> 'guests'));
       
       //example of a route with a callback
       $this->add("async",  "/async/(\w+)", array('index'=>'guests'), null, function($args){
        
            base\Headers::set(200, $args['format']);
            var_dump($args); 
        
        });

       //map routes
       $this->map()->route();

    }

}