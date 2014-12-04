<?php
session_start();
if(isset($_POST['checkcode']) && isset($_SESSION['checkcode']) && trim($_POST['checkcode']) != ''){
	if(strtoupper($_POST['checkcode']) == strtoupper($_SESSION['checkcode'])){
		echo '1';
		die();
	}
}
echo '0';