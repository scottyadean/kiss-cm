<?php
/**
* A Sample async Controller 
*/
class Async extends kiss\base\Controller { 

    /*
    * public index action
    * @return json
    */
    public function indexAction() {
       
       //return json using the base controllers _asJson method.
       print $this->asJson($this->params);
    
    }
    
 }