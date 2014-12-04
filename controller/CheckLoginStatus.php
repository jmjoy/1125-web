<?php

require_once '../config.inc.php';
require_once '../uc_client/client.php';

session_start();

if(isset($_SESSION['uid'], $_SESSION['username'])){
	show_info($_SESSION['uid'], $_SESSION['username']);
	die();
}
else{
	//TODO cookie 自动登陆 （userdefindui这个名字为了混淆试听）
	if(isset($_COOKIE['userdefindui'])){
		$str = cookie_unlock($_COOKIE['userdefindui']);
		$strArr = explode('|', $str);
		if(count($strArr) == 2){
			list($uid, $username, $password, $email) = uc_user_login($strArr[0], $strArr[1]);
			if($uid > 0) {
				session_start();
				$_SESSION['uid'] = $uid;
				$_SESSION['username'] = $username;
				show_info($uid, $username);
				die();
			}
		}
	}
}

//既没登陆又没登陆cookie， 显示登陆选项和注册选项
echo '<span class="btn" onclick="login()">登陆</span>';
echo '<span class="btn" onclick="location = \'register.html\' ">注册</span>';
echo '<script type="text/javascript" charset="utf-8">';
echo '
		$(".btn").css("display", "inline-block");
		$(".btn").css("margin", "5px");
		$(".btn").css("width", "60px");
		$(".btn").css("height", "60px");
		$(".btn").css("color", "#369");
		$(".btn").css("text-align", "center");
		$(".btn").css("line-height", "60px");
		$(".btn").css("border", "2px dotted #9cc");
		$(".btn").css("box-shadow", "5px 5px 10px #888");
		$(".btn").css("border-radius", "1000px");
		$(".btn").css("cursor", "pointer");
		
		$(".btn").mouseover(function(){$(this).css("zoom", "1.1");});
		$(".btn").mouseout(function(){$(this).css("zoom", "1");});
		
		function login(){
			if($("#cover").length == 0){
				addLoginFrame(350, 500);
			}
			else{
				$("#cover").show();
				$("#login_frame").show();
			}
		}
		
		function addLoginFrame(width, height){
			//遮掩布
			$("body").append("<div id=\'cover\' style=\'position: absolute; z-index: 3; top: 0; left: 0; width: 100%; height: 100%; background: #888; opacity: 0.8;\'></div>");
			var clientWidth = document.documentElement.clientWidth;
			var clientHeight = document.documentElement.clientHeight;
			var x = Math.floor((clientWidth - width) / 2);
			var y = Math.floor((clientHeight - height) / 2);
			//登陆框
			$("body").append("<div id=\'login_frame\' style=\' position: absolute; z-index: 4; \'></div>");
			$("#login_frame").css("top", y + "px");
			$("#login_frame").css("left", x + "px");
			$("#login_frame").css("width", width + "px");
			$("#login_frame").css("height", height + "px");
			$("#login_frame").css("padding", "10px");
			$("#login_frame").css("background", "#eee");
			$("#login_frame").css("border-radius", "10px");
			$("#login_frame").css("box-shadow", "10px 10px 5px #888");
			//登陆框里面的内容
			$("#login_frame").html("<div style=\' font-size: 1.3em; color: #555;\'>1125账号登陆<span onclick=\'hideLoginFrame()\' style=\' float: right; font-size: 1.3em; color: #888; cursor: pointer;\'>X</span><div style=\'clear: both; \'></div></div><hr /><div id=\'login_table\'></div>");
			$("#login_table").html("<div class=\'login_error\' id=\'login_error\'></div><p class=\' login_tip \'>↓ 请输入用户名或者邮箱地址</p><input id=\'username\' class=\'login_input\' /><p class=\' login_tip \'>↓ 请输入密码</p><input id=\'password\' class=\'login_input\' type=\'password\' /><p class=\' login_tip \'>↓ 请输入验证码, 不区分大小写, 点击图片换一张</p><input id=\'checkcode\' class=\'login_input\' />" + 
					" <img id=\'checkcode_pic\' alt=\'验证码\' title=\'换一张\' align=\'center\' style=\'line-height: 30px; cursor: pointer;\' src=\'controller/checkcode.php?t='.time().'\' /><span class=\'login_error\' id=\'checkcode_error\'></span><div style=\'margin:10px;\'><input style=\'zoom: 1.5;\' id=\'auto_login\' type=\'checkbox\' value=\'fuck\' /><label style=\'color: #888;\' for=\'auto_login\'>是否自动登录</label></div><button id=\'login_submit\'>登录</button>");

			$("#checkcode_pic").click(function(){$(this).attr("src", "controller/checkcode.php?t=" + new Date().getTime())});
			$(".login_error").css("margin", "5px 10px");
			$(".login_error").css("height", "30px");
			$(".login_error").css("line-height", "30px");
			$(".login_error").css("color", "#f00");
			$(".login_input").css("padding", "0 10px");
			$(".login_input").css("margin", "10px");
			$(".login_input").css("width", "250px");
			$(".login_input").css("height", "30px");
			$(".login_input").css("font-size", "1.2em");
			$(".login_input").css("border", "#aaa solid 1px");
			$(".login_input").css("border-radius", "5px");
			$("#checkcode").css("width", "80px");
			$(".login_tip").css("margin-left", "10px");
			$(".login_tip").css("font-size", "0.9em");
			$(".login_tip").css("color", "#888");
			$("#login_submit").css("margin-left", "10px");
			$("#login_submit").css("width", "100px");
			$("#login_submit").css("height", "30px");
			$("#login_submit").css("color", "#888");

			var isCheckCode = false;
			function checkCheckCode(){
				$.post("controller/checkCheckCode.php", {"checkcode" : $("#checkcode").val()}, function(data){
					if(data == 1){
						$("#checkcode_error").html("验证码填对了");
						$("#checkcode_error").css("color", "#080");
						isCheckCode = true;
					}
					else{
						$("#checkcode_error").html("验证码填错了");
						$("#checkcode_error").css("color", "#f00");
						isCheckCode = false;
					}
				});
			}				
			
			$("#checkcode").blur(function(){
				checkCheckCode();
			});
							
			//按"登录"之后
			$("#login_submit").click(function(){
				checkCheckCode();
				if(!isCheckCode) {return;}
				var username = $("#username").val();
				var password = $("#password").val();
				var checkcode = $("#checkcode").val();
				var auto_login = $("#auto_login").is (":checked");
				$.post("controller/LoginProcess.php", {"username" : username, "password" : password, "checkcode" : checkcode, "auto_login" : auto_login}, function(data){
					if(data == "false"){
						$("#login_error").html("用户名(邮箱地址)或密码错误！");
						$("#checkcode_pic").attr("src", "controller/checkcode.php?t=" + new Date().getTime());
						$("#checkcode").val("");
					}
					else{
						$("body").append(data);
						location = "index.html";
					}
				});
			});

		}
							
		//按X之后关闭登录面板
		function hideLoginFrame(){
			$("#cover").hide();
			$("#login_frame").hide();
		}
							
		';
echo '</script>';


/*
 * 展示头像和用户名字， 个人中心选项， 退出登陆选项
 */
function show_info($uid, $username){
	//表格展示头像和id等
	echo '<table style="padding: 0px 20px; margin-right: 10px; border: dotted 3px #9cc; border-radius: 10px; box-shadow: 5px 5px 4px #888;" cellspacing="2px">';
	echo '<tr><td colspan="2">';
	echo "<p style='color: #369; font-size: 1.1em;'>$username</p>";
	echo '</td></tr>';
	echo '<tr><td>';
	echo '<div style="position: relative;" id="avatar_frame">';
	echo '<a href="#"><img src=http://' . $_SERVER['HTTP_HOST'] . '/ucenter/avatar.php?uid=' . $uid . '&size=small /></a>';
	echo '</div>';
	echo '</td>';
	echo '<td style="padding-left: 10px;">';
	echo " <a style='font-size: 0.9em;' href='#'>[个人中心]</a>";
	echo '<br />';
	echo " <a style='font-size: 0.9em;' href='javascript:void(0)' onclick='logout()'>[退出登陆]</a>";
	echo '</td></tr>';
	echo '</table>';
	//js设置样式和头像大图效果
	echo '<script type="text/javascript" charset="utf-8">';
	echo '
			$("#avatar_frame").mouseover(function(){
				if($("#big_avatar").length == 0){ addBigAvatar(); }
				else{ $("#big_avatar").show(); }
			});
			
			$("#avatar_frame").mouseout(function(){
				if($("#big_avatar").length > 0) { $("#big_avatar").hide(); }
			});
			
			function logout(){
				$.post("controller/logoutProcess.php", function(data){
					$("body").append(data);
					location = "index.html";
				});
			}
			
			function addBigAvatar(){
				$("#avatar_frame").append("<div id=big_avatar></div>");
				$("#big_avatar").css("position", "absolute");
				$("#big_avatar").css("top", "0");
				$("#big_avatar").css("right", "50px");
				$("#big_avatar").css("z-index", "2");
				$("#big_avatar").html("<img style=\' border: 3px solid #9cc; border-radius: 10px; \' src=\' http://' . $_SERVER['HTTP_HOST'] . '/ucenter/avatar.php?uid=' . $uid. '&size=big \' />");
			}
			
			';
	echo '</script>';
	
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