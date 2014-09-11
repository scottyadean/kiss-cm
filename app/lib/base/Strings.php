<?php

namespace base;

class Strings {
    
    
    public static function camelCaseSplit($str){
        return preg_replace("/(([a-z])([A-Z])|([A-Z])([A-Z][a-z]))/","\\2\\4 \\3\\5", $str);
    }
    
    public static function actionRouterName($str) {
        return lcfirst(implode('', array_map('ucfirst', explode('-', strtolower($str)))));
    }
       
}
    