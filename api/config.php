<?php

$forceRemoteDB=true;

$whitelist = array(
    '127.0.0.1',
    '::1'
);

/*if(in_array($_SERVER['REMOTE_ADDR'], $whitelist) && !$forceRemoteDB){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname_main = "abilia_main";
    $dbname_dev = "abilia_dev";
    $dbname_settings = "abilia_settings";
    $current_server="local";
}else{*/
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname_main = "lamb_animation";
    $current_server="local";
//}




?>