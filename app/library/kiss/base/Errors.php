<?php

namespace kiss\base;

class Errors {
    
    
    public static function Error($errno, $errstr, $errfile, $errline){
        
        $route = new Router;
        $route->format = 'html';
        $route->template = new Template();
        $route->controller = 'error';
        $route->action = 'error';
        $route->title = "Error ".$errno;
        $route->errors[] = self::formatException($errno, $errstr, $errfile, $errline);
        
        print $route->template->mvc($route->getMvcConfig(array("file"=>$errfile, "line"=>$errline, "error"=>$errstr  )));
        exit;
    }
    
    
    public static function formatException($errno, $errstr, $errfile, $errline) {
        
            
    if (!(error_reporting() & $errno)) {
         // This error code is not included in error_reporting
         return "Unknown error type: [$errno] $errstr<br />\n";
     }
 
     switch ($errno) {
         
         case E_USER_ERROR:
             $msg  = "<b>My ERROR</b> [$errno] $errstr<br />\n";
             $msg .= "Fatal error on line $errline in file $errfile";
             $msg .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
             $msg .= "Aborting...<br />\n";
             return $msg;
         break;
     
         case E_USER_WARNING:
             return "<b>My WARNING</b> [$errno] $errstr<br />\n";
         break;
     
         case E_USER_NOTICE:
             return "<b>My NOTICE</b> [$errno] $errstr<br />\n";
         break;
     
         default:
             return "Unknown error type: [$errno] $errstr<br />\n";
         break;
     }
 
     /* Don't execute PHP internal error handler */
     return;    
        
        
    }
    
    
    
    
} 