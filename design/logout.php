<?php 
session_start();

// セッションを削除
$_SESSION=array();
if(ini_get("session.use_cookies")){
	$params=session_get_cookie_params();
	setcookie(session_name(),'',time() -42000,$params['path'],$params['domain'],$params['secure'],$params['httponly']);
}

session_destroy();

// cookie情報も削除
setcookie('email','',time() -36000);
setcookie('password','',time() -36000);

header('Location: index.php');
exit();



?>