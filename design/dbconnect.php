<?php    

// ロリポップ用
// $db=mysqli_connect('mysql119.phy.lolipop.lan','LAA0840129','mysql','LAA0840129-snsservice') or die(mysqli_connect_error());

// mysqli_set_charset($db,'utf8');


// ローカル用
$db=mysqli_connect('localhost','root','','sns-service') or die(mysqli_connect_error());

mysqli_set_charset($db,'utf8');




?>