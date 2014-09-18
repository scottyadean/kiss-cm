<?php

namespace kiss\base;

class Errors {
    
    
    public static function Error($exception){
        
        $route = new  \kiss\base\Routes;
        $route->init();
        $route->action = $route->controller = 'error';
        $route->errors[] = 'Kiss Application Error';
        $route->routeShutDownError($exception);
        
        exit;
    }
    

    
    
    
} 