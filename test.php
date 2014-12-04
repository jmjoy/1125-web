<?php

require_once 'config.inc.php';
require_once 'uc_client/client.php';

echo uc_user_synlogin(3);

// session_start();
// $_SESSION['checkcode'] = create_check_code();
// create_check_code();

?>