<?php
require_once '../lib/checkcode.php';

session_start();
$_SESSION['checkcode'] = create_check_code();
?>