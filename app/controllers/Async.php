<?php
/**
* A Sample async Controller 
*/
use kiss\base\Controller as baseController;
class Async extends  baseController { 
    /*
    * public index action
    * @return json
    */
    public function indexAction() {
       //return example json using the base controllers _asJson method.
       echo $this->toJson($this->params);
    }
    
 }