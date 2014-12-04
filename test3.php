<?php

require_once './config.inc.php';
require_once './uc_client/client.php';

$a =  cookie_lock('jmjoy|xjm19921125');
echo cookie_unlock($a); 
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

/*
 * cookie解密
*/
function cookie_unlock($str){
	$strArr = str_split($str);
	$str = '';
	//有效位 1 2 5 6 10 11 12 13 14 15 17 19 以后
	$str .= $strArr[1] . $strArr[2] . $strArr[5] . $strArr[6] . $strArr[10] . $strArr[11] . $strArr[12] . $strArr[13] . $strArr[14] . $strArr[15] . $strArr[17] . $strArr[19];
	
	for($i = 20; $i < count($strArr); $i++){
		$str .= $strArr[$i];
	}
	
	$str = strrev($str);
	return uc_authcode($str);
}