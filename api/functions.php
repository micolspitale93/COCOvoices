<?php
define('APACHE_MIME_TYPES_URL','vendors/mime.types.txt');

function getMediaType($url){
    
    $type=array();
    $type["IMG"]=array("png","jpe","jpeg","jpg","gif","bmp","ico","tiff","tif","svg","svgz");
    $type["SND"]=array("mp3","wav");
    $type["VID"]=array("mp4","avi","mov");
    $type["TXT"]=array("txt");
    $type["TXT_JSON"]=array("json");
    $type["TXT_XML"]=array("xml");
    
    if (youtube_id_from_url($url)!=false){
        return array("VID","YT","mp4");
    }else{
        $mimetype=get_headers($url, 1)["Content-Type"];
       
        $mimetype=explode(";",$mimetype)[0];
        $arrayofmimetype=WhichExtension();
        $ext=$arrayofmimetype[$mimetype];

        foreach($type as $key=>$value){
            if(in_array($ext,$type[$key])) {
                $type_subtype=explode("_",$key);
                if(count($type_subtype)>1) return array($type_subtype[0],$type_subtype[1],$ext);
                else return array($type_subtype[0],"-",$ext);
            }
        }

        $ext = pathinfo($url, PATHINFO_EXTENSION); //another possibility, now trying to get info from url
        foreach($type as $key=>$value){
            if(in_array($ext,$type[$key])) {
                $type_subtype=explode("_",$key);
                if(count($type_subtype)>1) return array($type_subtype[0],$type_subtype[1],$ext);
                else return array($type_subtype[0],"-",$ext);
            }
        }
    }    
    return array(null,null,null);

}

function downloadMedia($id,$url,$ext){
    if(youtube_id_from_url($url)!=false) return true;
    return file_put_contents(RESOURCE_FOLDER.$id.".".$ext, fopen($url, 'r'));
}

function WhichExtension(){
    $url=APACHE_MIME_TYPES_URL;
    $s=array();
    foreach(@explode("\n",@file_get_contents($url))as $x)
        if(isset($x[0])&&$x[0]!=='#'&&preg_match_all('#([^\s]+)#',$x,$out)&&isset($out[1])&&($c=count($out[1]))>1)
            for($i=1;$i<$c;$i++)
                $s[$out[1][0]]=$out[1][$i];
    
    return $s;
}

function curl_get_contents($url)
{
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
  $data = curl_exec($curl);
  curl_close($curl);
  return $data;
}

function youtube_id_from_url($url) {
   $pattern =
    '%^# Match any youtube URL
    (?:https?://)?  # Optional scheme. Either http or https
    (?:www\.)?      # Optional www subdomain
    (?:             # Group host alternatives
      youtu\.be/    # Either youtu.be,
    | youtube\.com  # or youtube.com
      (?:           # Group path alternatives
        /embed/     # Either /embed/
      | /v/         # or /v/
      | .*v=        # or /watch\?v=
      )             # End path alternatives.
    )               # End host alternatives.
    ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
    ($|&).*         # if additional parameters are also in query string after video id.
    $%x'
    ;
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result && count($matches)!=0) {
        return $matches[1];
    }
    return false;
 }

?>