<?php

    ini_set ("display_errors" , "On" );
    
    $loader = require '../app/library/vendor/autoload.php';
    $loader->add("kiss", __DIR__.'/../app/library/');
 
    //create a new app
    $kiss = new \kiss\base\App(realpath(dirname(__FILE__)));
 
    //display the output
    print $kiss->response;