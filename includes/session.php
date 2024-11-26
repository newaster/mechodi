<?php
session_start();

if( session_id() == "" ){
    session_start();
}

class Session {

 public $msg;
 private $user_is_logged_in = false;

 function __construct(){
   $this->flash_msg();
   $this->userLoginSetup();
 }

  public function isUserLoggedIn(){
    return $this->user_is_logged_in;
  }
  public function login($user_id){
    $_SESSION['user_id'] = $user_id['id'];
    $_SESSION['username'] = $user_id['uname'];
    $_SESSION['number'] = $user_id['number'];
    $_SESSION['email'] = $user_id['email'];
    $_SESSION['company'] = $user_id['company'];
    $_SESSION['name'] = $user_id['fname']." ".$user_id['lname'];
    $_SESSION['role'] = $user_id['role'];
    $_SESSION['zone'] = $user_id['sites'];
    $_SESSION['user_admin'] = $user_id['updated_by'];
    $_SESSION['cumulative'] = $user_id['param'];
  }
  private function userLoginSetup()
  {
    if(isset($_SESSION['user_id']))
    {
      $this->user_is_logged_in = true;
    } else {
      $this->user_is_logged_in = false;
    }

  }
  public function logout(){
    unset($_SESSION['user_id']);
  }

  public function msg($type ='', $msg =''){
    if(!empty($msg)){
       if(strlen(trim($type)) == 1){
         $type = str_replace( array('d', 'i', 'w','s'), array('danger', 'info', 'warning','success'), $type );
       }
       $_SESSION['msg'][$type] = $msg;
    } else {
      return $this->msg;
    }
  }

  private function flash_msg(){

    if(isset($_SESSION['msg'])) {
      $this->msg = $_SESSION['msg'];
      unset($_SESSION['msg']);
    } else {
      $this->msg;
    }
  }
}

//$session = new Session();
//$msg = $this->msg();

?>
