<?php
use kiss\base\Controller   as BaseController;
use models\activerecord    as ActiveModels;
use kiss\base\auth\Hash    as Hash;
use kiss\base\auth\Session as Sess;

//forms
use \models\forms\Join  as JoinForm;
use \models\forms\Login as loginForm;

class Auth extends BaseController { 

    /**
     * publc __construct
     * put init code here
    */
    public function init() {
    }
 
    /**
     * publc index Action
     * view @  /views/auth/login.phtml
    */
    public function indexAction() {
        $this->view = 'auth/login';
        $this->loginAction();
    }

    /**
     * publc join Action
     * view @  /views/auth/join.phtml
    */
    public function joinAction() {
        
        $join = new joinForm('/auth/join');
        $usr = null;
        
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
                $join->form->clearSession();             
                $join->form->process();
        
            }
        }
       
        $this->authError = empty($usr) ? false : true;
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
        
        $login = new loginForm;
        
        $this->authError = false;
        
        if($this->isPost() && $login->form->validate()) {
            
            $usr = ActiveModels\User::find_by_username_or_email(trim($_POST['username']),
                                                                trim($_POST['username']));
            if(!empty($usr)){
               
               $auth = Hash::GetHmac(trim($_POST['password']), $usr->salt, $usr->hash);
               
               if( $auth == true ) {
                    Sess::Invoke($usr);
                    $login->form->clearSession();
                    $login->form->process();
               }
            }
            
            $this->authError = true;        
        }
        
        
        $this->form = $login->getForm();
    }

    public function passAction() {
        
        if($this->isPost()) {   
        
            $usr = ActiveModels\User::find_by_username_or_email(trim($_POST['username']),
                                                                trim($_POST['username']));
        }
        
    }


    
    /**
    * logout action
    * - destroy session.
    * - redirect to the base route
    **/
    public function logoutAction() {
        Sess::Destroy();
        $this->_redirect("/");
    }
}