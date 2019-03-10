<?php

include_once "config.php";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname_main);

// Check connection
if ($mysqli->connect_error) die(__LINE__." - 901 - db_main error: ".$mysqli->error);
die(__LINE__." - OK - connected");

?>