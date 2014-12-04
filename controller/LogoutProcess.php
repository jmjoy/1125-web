<?php

require_once '../config.inc.php';
require_once '../uc_client/client.php';

setcookie('userdefindui', '', -1024);
echo uc_user_synlogout();