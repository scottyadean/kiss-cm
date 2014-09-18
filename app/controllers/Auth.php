<?php

use kiss\base\Controller as BaseController;
use models\activerecord as ActiveModels;
use kiss\base\auth\Hash as Hash;

class Auth extends BaseController { 
 
    protected $_userModel;
 
    /**
     * publc __construct
     * put init code here
    */
    public function init() {
    
    }
 
    /**
     * publc index Action
     * view @  /views/index/index.phtml
    */
    public function indexAction() {
        $this->view = 'auth/login';
        $this->loginAction();
    }

    public function joinAction() {
        
        $join = new \models\forms\Join('/auth/join');
        
        if($this->isPost() && $join->form->validate()) {
         
            $usr = ActiveModels\User::find_by_username_or_email($this->getParam('username'),
                                                                $this->getParam('email'));
            //if no user exists go ahead and create a user.            
            if(empty($usr)){
                
                $hash = Hash::SetHmac(trim($_POST['password']));
                
                $data = array( 'username' => $this->getParam('username'),
                               'password' => 'xxxxxxxx',
                               'email'    => $this->getParam('email'),
                               'verified' => 0,
                               'hash'     => $hash['hash'],
                               'salt'     => $hash['salt'],
                               'role'     => 1);
                
                $model = new ActiveModels\User;
                $model->_create($data);
                $join->form->process();
            }
        }
        
        $this->authError = $this->isPost() && empty($usr) ? true : false;
        $this->form = $join->getForm();
        
    }
    
    /**
     * pub login action
     * - create login from
     * - check for post
     * - validate form values
     * - check if user found
     * - if auth rediect else display login form
    */
    public function loginAction() {
        
        $login = new \models\forms\Login;
        
        $this->authError = false;
        
        if($this->isPost() && $login->form->validate()) {
            
            $usr = ActiveModels\User::find_by_username_or_email(trim($_POST['username']),
                                                                trim($_POST['username']));
            
            if(!empty($usr)){
               
               $auth = Hash::GetHmac(trim($_POST['password']), $usr->salt, $usr->hash);
               
               if( $auth == true ) {
                    $_SESSION['role'] = $usr->role;
                    $_SESSION['-u']   = array('id'=>$usr->id,
                                              'username'=>$usr->username,
                                              'email'=>$usr->email,
                                              'role'=>$usr->role);
                    $login->form->process();
               }
            }
        
            $this->authError = true;
            
        }
        
        
        $this->form = $login->getHtml();
    }
    
    /**
    * logout action
    * - destroy session.
    **/
    public function logoutAction() {
        
        if(isset($_SESSION['role']))
            unset($_SESSION['role']);
        
        if(isset($_SESSION['-u']))
            unset($_SESSION['-u']);
        
        if ( isset( $_COOKIE[session_name()] ) )
            setcookie( session_name(), "", time()-3600, "/" );
        
        $_SESSION = array();
        
        session_destroy();
        
        $this->_rediect("/index"); 
    }
    
    
    
}