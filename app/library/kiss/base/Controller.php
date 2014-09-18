<?php

namespace kiss\base;

class Controller {
    
    public $view;
    public $scope;
    public $form;
    public $action;
    public $params;
    public $layout = '_index';
    public $controller;
    
    //set in the controller to disable layout or view
    public $disable_view;
    public $disable_layout;
    
    public $scripts = array();
    public $styles = array();
    
   /**
    * pub init
    * placeholder mvc calls init by default if the child class
    * does not have an init function the the parment init will
    * be called.
    * @return void
    **/
    
    public function init() {
    }    
   
   /**
    * pub initProps
    * set mvc vars props to this object
    * @return object return this context to the layout and view
    **/
   public function initProps(array $values) {
        
        foreach( $values as $k=>$v ) {
            $this->$k = $v;
        }
        
        return $this; 
    }
   
   /**
    * pub getMvc
    * @return object return this context to the layout and view
    **/      
    public function getMvc() {
         return $this;
    }
    
   /**
    * pub getLayout
    * @return string  path to the the template file
    **/      
    public function getLayout() {
        
        return $this->layout;  
    }
    
    /**
     * pub toJson
     * @param $array array to parse into json
     * @param $disable_view bool display the view render
     * @param $disable_layout bool display the layout render
     * @return json string
    */
    public function toJson(array $array,
                           $disable_view = true,
                           $disable_layout = true) {
       
       $this->disable_layout = $disable_layout;
       $this->disable_view = $disable_view;  
       
       return json_encode($array);
    }
    
    /**
     * pub getScripts
     * @param $path string path to your css file
     * @param $attributes string any extra attributes to append to the script element
     * @return bool
    */
    public function setScript($path, $attributes="") {
        $this->scripts[$path] = $attributes;
        return true;
    }
    
    /**
     * pub getScripts
     * append js files to the mvc view or layout
     * @return string
    */
    public function getScripts() {
        $tags = '';
        foreach($this->scripts as $js=>$attributes) {
            $tags .= "<script src='{$js}' $attributes></script>\n";
        }
        return $tags;
    }
    
    /**
     * pub setStyles
     * @param $path string path to your css file
     * @param $attributes string any extra attributes to append to the link element
     * @return bool
    */
    public function setStyle($path, $attributes ='') {
        $this->styles[$path] = $attributes;
        return true;
    }
    
    /**
     * pub getStyles
     * return style sheet links
     * @return string
    */
    public function getStyles() {
        $tags = '';
        foreach($this->styles as $css=>$attributes) {
            $tags .= "<link rel='stylesheet' href='{$css}' {$attributes}>\n";
        }
        return $tags;
    }

    /**
     * pub setSession
     * @param $index string the index key to set in the $_SESSION array
     * @param $value mixed value to set in session
     * @param $tidy bool remove html tags and trim value by default
     * @return bool
    */
    public function setSession($index, $value, $tidy=true) {
        $_SESSION[$index] = $tidy ? htmlentities(trim($value)) : $value;
        return $_SESSION[$index];
    }

    /**
     * pub getSession
     * @param $index string the index key in the $_SESSION array
     * @param $default mixed default value to return is empty
     * @param $tidy bool remove html tags and trim value by default
     * @return mixed
    */
    public function getSession($index, $default=null, $tidy=false) {
       
       if(isset($_SESSION[$index])  && !empty($_SESSION[$index])) {
            return $tidy ? htmlentities(trim($_SESSION[$index])) : $_SESSION[$index];
       }
       return $default;
    }
    
    /**
     * pub getParam
     * Return if the cleaned param otherwise return default
     * @param $index string the index key in the $_REQUEST array
     * @param $default mixed default value to return is empty
     * @param $tidy bool remove html tags and trim value by default
     * @return mixed
    */
    public function getParam($index, $default=null, $tidy=true) {
       
       if(isset($this->params[$index])  && !empty($this->params[$index])) {
        return $tidy ? htmlentities(trim($this->params[$index])) : $this->params[$index];
       }
       
       if(isset($_REQUEST[$index])  && !empty($_REQUEST[$index])) {
        return $tidy ? htmlentities(trim($_REQUEST[$index])) : $_REQUEST[$index];
       }
       
       return $default;
    }
    
    /**
     * pub getUser
     * if the user is logged you can return attributes based on scope of the -u namespace.
     * @param $index string the index key in the -u session array (id, username, email).
     * @return string
     **/
    public function getUser($index = "username") {
        return isset($_SESSION['-u'][$index]) ? $_SESSION['-u'][$index] : null;
    }
    
    /*
     * Return true if the request is post
     * @return<bool>
    */
    public function isPost() {
        return (!empty($_SERVER['REQUEST_METHOD'])
                &&  strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') ? true : false;
    }
    
    /**
     * pub isXhr
     * Return true if the request is ajax
     * @return bool
    */
    public function isXhr()  {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
    }

    /**
     * pub isLogged
     * Check to see if the session role is set.
     * Note: acl will take care of router access
     * do not use this function to grant access to resources
     * only use it to display html toggled by logged users.
     * ie (logout btn vs. login form)
     * @return bool, int
    */
    public function isLogged() {
        return !isset($_SESSION['role']) || $_SESSION['role'] == 0 ? false : true;   
    }
    
    /**
     * pro _redirect
     * redirect to a local route.
     * @param $path string route to rediect to
     * @return void 
    */
    protected function _redirect($path) {
        header( 'Location: '.$path );
        exit;   
    }
}