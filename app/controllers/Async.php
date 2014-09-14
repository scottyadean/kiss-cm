<?php
/**
* A Sample async Controller 
*/

use kiss\base;

class Async extends Controller { 

    /*
    * public index action
    * @return json
    */
    public function indexAction() {
       
       //return json using the base controllers _asJson method.
       print $this->_asJson($this->params);
    
    }
    
 }