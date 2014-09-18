<?php

    ini_set ("display_errors" , "On" );
    ini_set('session.cookie_lifetime',  10800);
    
    $loader = require '../app/library/vendor/autoload.php';    
    
    $loader->add("kiss", __DIR__.'/../app/library/');
    $loader->add("models", __DIR__.'/../app/');
    
    //create a new app
    $kiss = new \kiss\base\App(realpath(dirname(__FILE__)));
 
    //display the output
    print $kiss->response;