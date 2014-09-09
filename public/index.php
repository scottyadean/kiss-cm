<?php
  
    //set up the autoloader
    function __autoload($class) {
       require_once str_replace("\\", DIRECTORY_SEPARATOR, "..\\app\\lib\\". $class. ".php");
    }
    
    //create a new app
    $app = new base\App(realpath(dirname(__FILE__)));
   
    //invoke the rest object.
    $rest = new Rest(new Routes());

    //Return response to context
    print $rest->response();