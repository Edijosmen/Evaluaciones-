<?php require_once BASE_PATH.'/app/DB.php';
class Eval{
 function index(){
  $db=DB::c();
  $e=$db->query("SELECT * FROM evaluations")->fetchAll();
  require BASE_PATH.'/views/evals.php';
 }
 function answer(){
  $db=DB::c();
  $db->prepare("INSERT INTO responses(user_id,evaluation_id) VALUES(?,?)")
    ->execute([$_SESSION['u']['id'],$_POST['id']]);
  echo 'OK';
 }
}
