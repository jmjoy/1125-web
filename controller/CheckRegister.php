<?php

if(isset($_GET['mod']) && isset($_POST['val'])){
	require_once '../config.inc.php';
	require_once '../uc_client/client.php';
	
	$val = $_POST['val'];
	switch($_GET['mod']){
		
		case 'username':
			echo uc_user_checkname($val);
			break;
			
		case 'email':
			echo uc_user_checkemail($val);
			break;
			
		case 'checkcode':
			$_POST['checkcode'] = $val;
			require_once 'CheckCheckCode.php';
			break;
	}
}