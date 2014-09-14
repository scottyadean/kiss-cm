<?php
/**
* A Sample async controler / action.
*/
class Async extends kiss\base\Controller { 

    /*
    * public index action
    * @return json
    */
    public function indexAction() {
       
       //return json using the base controllers _asJson method.
       print $this->_asJson($this->params);
    }
    
   
 }
