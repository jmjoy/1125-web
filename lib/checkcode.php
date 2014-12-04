<?php

const WIDTH = 100;
const HEIGHT = 30;
const WORD_X_START = 10;
const WORD_X_PRE = 22;
const WORD_Y = 20;
const WORD_SIZE = 14;
const ANGLE = 30;
const TTF_PATH = 'MSYHBD.TTF';

/**
 * 返回一个验证码的字符串
 */
function create_check_code(){
	
	//随机获取一个记录验证码的四位数组
	$chs = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
	$rand_keys = array_rand($chs, 4);
	$strs = array();
	foreach($rand_keys as $key){
		$strs[] = $chs[$key];
	}
	//画图
	$image = imagecreatetruecolor(WIDTH, HEIGHT);
	if(!$image) die();
	
	$black = imagecolorallocate($image, 0, 0, 0);
	$grey = imagecolorallocate($image, 200, 200, 200);
	
	$colors = array();
	$colors[] = imagecolorallocate($image, 255, 0, 0);
	$colors[] = imagecolorallocate($image, 255, 128, 0);
	$colors[] = imagecolorallocate($image, 0, 0, 255);
	$colors[] = imagecolorallocate($image, 128, 0, 255);
	$colors[] = imagecolorallocate($image, 0, 125, 0);
	$colors[] = imagecolorallocate($image, 125, 125, 0);
	
	imagefill($image, 0, 0, $grey);
	imagerectangle($image, 0, 0, WIDTH - 1, HEIGHT - 1, $black);
	for($i = 0; $i < 3; $i++){
		$color = $colors[array_rand($colors)];
		imageline($image, rand(0, WIDTH - 1), 0, rand(0, WIDTH - 1), HEIGHT - 1, $color);
		$color = $colors[array_rand($colors)];
		imageline($image, 0, rand(0, HEIGHT - 1), WIDTH - 1, rand(0, HEIGHT - 1), $color);
	}
	foreach($strs as $key=>$val){
		$color = $colors[array_rand($colors)];
		imagettftext($image, WORD_SIZE, rand(-ANGLE, ANGLE), WORD_X_START + WORD_X_PRE * $key, WORD_Y, $color, TTF_PATH, $val);
	}
	
	Header('Content-Type: image/gif');
	imagegif($image);
	
	imagedestroy($image);
	
	return implode($strs);
}
?>