<?php require_once BASE_PATH.'/app/DB.php';
class Auth{
 function login(){ require BASE_PATH.'/views/login.php'; }
 function do(){
  $db=DB::c();
  $s=$db->prepare("SELECT * FROM users WHERE email=?");
  $s->execute([$_POST['email']]);
  $u=$s->fetch();
  if($u && password_verify($_POST['password'],$u['password'])){
    $_SESSION['u']=$u;
    header('Location: evals'); exit;
  } else echo 'Error login';
 }
}
