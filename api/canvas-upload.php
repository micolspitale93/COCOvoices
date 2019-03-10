<?php

header("Access-Control-Allow-Origin: *");
error_reporting(E_ALL);
ini_set('display_errors', 1);
	
	$img = $_POST['imgBase64'];
    $nameImg = $_POST['nameImg'];
	$img = str_replace('data:image/png;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	$file = $nameImg.'.png';
    $UPLOAD_DIR="./uploads/";
  
	$success = file_put_contents($UPLOAD_DIR.$file, $data);
	print $success ? $file : 'Unable to save the file.';
?>