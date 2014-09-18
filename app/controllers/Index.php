<?php

//TO use active record orm.
use models\activerecord as models;

//TO USE propel orm
//use models\propel as models;

class Index extends kiss\base\Controller { 
 
    /**
     * publc __construct
     * put init code here
    */
    public function __construct() { 
    }
 
    /**
     * publc index Action
     * view @  /views/index/index.phtml
    */
    public function indexAction() {
    }
    
    /**
     * publc aboutUs Action
     * Sample - camelCase aboutUs method will map to view about-us 
     * view @ /views/index/about-us.phtml
    */
    public function aboutUsAction() {
       //you can swop the layout and view in the controller..
       $this->layout = "_index";
       $this->view = 'index/test';
    
    }
    
}
