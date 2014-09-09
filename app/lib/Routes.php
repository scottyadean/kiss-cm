<?php

  class Routes extends base\Routes {
    
    

  
    public function __construct() {
      $this->template = new Template();      
      $this->routes  = array(  
                             'index' => array('actions'=>array('index')),
                             'sync'  => array('actions'=>array('index')));
    }
    
    public function index() {
       
       //set the header and stats code.
       $this->_setHeader(200, 'html');
       
     
       return $this->template->parseController( array( 'controller' => 'Index',
                                                       'action'=>$this->action,
                                                       'view'=>'index/'.$this->action,
                                                       'scope'=>array("title"=>"Welcome"),
                                                       'params'=>$this->_params));
    }

    public function sync() {
       
       //set the header and stats code.
       $this->_setHeader(200, 'json');
       
       return $this->template->parseController( array( 'controller' => 'Sync',
                                                       'action'=>$this->action,
                                                       'layout'=>"_blank",
                                                       'view'=>'sync/'.$this->action,
                                                       'scope'=>array("title"=>"Sync"),
                                                       'params'=>$this->_params));
    }
        
    
  }
