<?php

class Sync extends kiss\base\Controller { 

    public function indexAction() {
       print $this->_asJson($this->params);
    }



    public function infoAction() {
      print $this->_asJson($this->params);
        
    }    
    
   
 }
