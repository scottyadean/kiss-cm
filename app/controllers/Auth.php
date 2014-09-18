<?php

//use base controller
use kiss\base\Controller as BaseController;

//TO use active record orm.
use models\activerecord as models;

use kiss\base\auth\Hash as Hash;

class Auth extends BaseController { 
 
    protected $_userModel;
 
    /**
     * publc __construct
     * put init code here
    */
    public function init() {
        
        //$this->_userModel = new models\User;
    }
 
    /**
     * publc index Action
     * view @  /views/index/index.phtml
    */
    public function indexAction() {

       
    }

    
    public function joinAction() {
        
        /*
         *
         *Example user create.
        if($_POST) {
         
            $hash = Hash::SetHmac(trim($_POST['password']));
            
            $data = array( 'username'=> $_POST['username'],
                           'password'=> $hash['password'],
                           'hash'    => $hash['hash'],
                           'salt'    => $hash['salt'],
                           'role'    => 0
                          );
            
            
            $this->_userModel ->_create($data);
         
        }
        */
    }
    
    
    
    public function loginAction() {
        
        /* example auth..
        if($_POST){
            
            
            $usr = models\User::find_by_username(trim($_POST['username']));
            
            if(!empty($usr)){
            
               $auth = Hash::GetHmac($usr->password, $usr->salt, $usr->hash);
            
               if( $auth == true ) {
                
                    $_SESSION['role'] = $usr->role; 
                
               }
            
            }
        
        }
        */
        
        
        
    }
    
    
    
    
}