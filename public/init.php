<?php


    //create an instance of the autoloader.
    $loader = require '../app/library/vendor/autoload.php';    
    $loader->add("kiss", __DIR__.'/../app/library/');
    $loader->add("models", __DIR__.'/../app/');
    
    //create a new kiss app
    $kiss = new \kiss\base\App(realpath(dirname(__FILE__)));
 
    //display the output
    print $kiss->response;