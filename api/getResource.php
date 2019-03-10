<?php

//INCLUDED in index.php

header("Access-Control-Allow-Origin: *");

define('RESOURCE_FOLDER','../resources/');
include_once "functions.php";

$file_name = (array_key_exists("id",$_GET)) ? $_GET["id"] : die("null_id");
$typet = (array_key_exists("type",$_GET)) ? $_GET["type"] : die("null_type");

$file_url = RESOURCE_FOLDER . $file_name . ".*";
$result = glob ($file_url);

if($result){
    
    $server='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/";
    $url=$server.$result[0];
    list($type,$subtype,$extension)=getMediaType($url);
    //($type==$typet) or die("wrong_type");
    $file_name.=".".$extension;
    header("Location:".$url);

    exit();
    
}else if($typet=="YT" || $typet=="VID"){

    $serverlist = ['helloacm.com', 'happyukgo.com', 'uploadbeta.com', 'steakovercooked.com'];
    $server = "helloacm.com";
    $hash = "4aed460986194a800820aa09a0877bac";
    $url = "https://" . $server . "/api/video/?cached&lang=en&hash=".$hash."&video=";    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "http://ludomi.i3lab.me/api/",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\r\n    \"request\": \"getResourceInfo\",\r\n    \"email\": \"email\",\r\n    \"token\": \"token\",\r\n    \"data\": {  \r\n    \t\"id\": \"".$file_name."\"\r\n    }\r\n}"
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      die("err_curl_".$err);
    } 

    $response=json_decode($response,true);
    //var_dump($response["data"][0]["input"]);
    if($response["data"][0]["input"]){
        $url.=$response["data"][0]["input"];
    }else{
        die("err_resource_input");
    }

    $data=curl_get_contents($url);
    $data=json_decode($data,true);

    if($data["url"]){
        header("Location:".$data["url"]);
    }else{
        die("error");
    }
}
    
die("NULL_FILE");

?>