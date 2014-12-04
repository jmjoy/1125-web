<?php

//检测登陆的参数是否都存在
if(!isset($_POST['username'], $_POST['password'], $_POST['checkcode'], $_POST['auto_login'])){
	echo 'false'; die();
}
$username = $_POST['username'];
$password = $_POST['password'];
$checkcode = $_POST['checkcode'];
$auto_login = $_POST['auto_login'];

//检测登陆参数是否为空
foreach(array($username, $password, $checkcode, $auto_login) as $val){
	if(trim($val) == ''){
		echo 'false'; die();
	}
}

//检测验证码是否正确（防小人）
session_start();
if(!isset($_SESSION['checkcode']) || strtoupper($checkcode) != strtoupper($_SESSION['checkcode'])){
	echo 'false'; die();
}

//使用ucenter的登陆函数
require_once '../config.inc.php';
require_once '../uc_client/client.php';

//用户名 还是 邮箱 登陆
if(preg_match('/@/', $username)){
	list($uid, $username, $password, $email) = uc_user_login($username, $password, 2); //2 代表邮箱登陆
}
else{
	list($uid, $username, $password, $email) = uc_user_login($username, $password);
}

if($uid <= 0) {
	echo 'false'; die();
}

if($auto_login == 'true'){
	// cookie 自动登陆 （userdefindui这个名字为了混淆试听）
	$serect = "$username|$password";
	$serect = cookie_lock($serect);
	setcookie('userdefindui', $serect, time() + 365 * 24 * 60 * 60);
}

//同步登陆
echo uc_user_synlogin($uid);


/*
 * cookie加密
 */
function cookie_lock($str){
	$chs = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
	$serect = uc_authcode($str, 'ENCODE');
	$serect = strrev($serect);
	$strArr = str_split($serect);

	$serect = '';
	//密位 0 3 4 7 8 9 16 18
	$serect .= $chs[array_rand($chs)] . $strArr[0] . $strArr[1] . $chs[array_rand($chs)] . $chs[array_rand($chs)] .
	$strArr[2] . $strArr[3] . $chs[array_rand($chs)] . $chs[array_rand($chs)] . $chs[array_rand($chs)] .
	$strArr[4] . $strArr[5] . $strArr[6] . $strArr[7] . $strArr[8] . $strArr[9] .  $chs[array_rand($chs)] .
	$strArr[10] . $chs[array_rand($chs)];

	for($i = 11; $i < count($strArr); $i++){
		$serect .= $strArr[$i];
	}
	
	return $serect;
}