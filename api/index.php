<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$RAWPostData = file_get_contents("php://input");
$METHOD=json_decode($RAWPostData,true);

$METHOD=$_POST;

error_reporting(E_ALL & ~E_STRICT);
ini_set("display_errors", 1);
set_error_handler("HandleError", E_ALL & ~E_STRICT); //handle error
$log = array("info" => "./logs/info.html","error" => "./logs/error.html","success" => "./logs/success.html");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: application/xml; charset=utf-8");
header('Content-Type: application/json');
session_start();

include_once "config.php";
include_once "functions.php";
//include_once "session.php";

$DEBUG=true;
define("DEFAULT_DATETIME_EMPTY", "0000-00-00 00:00:00");
define("DEFAULT_DATETIME_NOW", gmdate("Y-m-d H:i:s"));//Greenwich Time
define("DEFAULT_DATE_EMPTY", "0000-00-00");
define("DEFAULT_DATE_NOW", gmdate("Y-m-d"));//Greenwich Time
define("NONE", "");
//define('APACHE_MIME_TYPES_URL','http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types');

define('RESOURCE_FOLDER','../resources/');



//$_HEADER = getRequestHeaders(); /*TO DO: pass the token through header*/
if(!isset($METHOD["request"]) || $METHOD["request"]=="") {
    $RAWPostData = file_get_contents("php://input");
    //var_dump($RAWPostData);
    $METHOD=json_decode($RAWPostData,true);
    //var_dump($METHOD);
    if(!isset($METHOD["request"]) || $METHOD["request"]=="") print_warning(__LINE__,"101"); //no request defined
}


// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname_main);
// Check connection
if ($mysqli->connect_error) print_warning(__LINE__,"901","db_main error");


/*
role
10) superadmin
9) admin
8) superdeveloper
7) developer
6) supervisor
5) server
4) observer
0) all
*/

//addToHistory();

switch ($METHOD["request"]) {
    case "getApiToken":
        // 
        checkFields($METHOD,["email","secret"]);
        $query="SELECT token,creation FROM auth WHERE email=? AND secret=? AND enabled=1";
        $stmt = $mysqli_dev->prepare($query) or print_warning(__LINE__,"902","error preparing query");
        $stmt->bind_param("ss", $METHOD["email"],$METHOD["secret"]);
        if($METHOD["email"]=="email") print_warning(__LINE__,"000","You can't change the default user, ask the administrator for you credentials");
        if (api_select_bool($stmt)){
            if ($stmt = $mysqli_dev->prepare("UPDATE auth SET token = ?, creation=now() WHERE email=?")) {
                $date = new DateTime();
                $token=md5(uniqid(rand(), true).$date->getTimestamp());
                $stmt->bind_param("ss", $token, $METHOD["email"]);
                $stmt->execute();
                $query="SELECT token,creation FROM auth WHERE email=? AND secret=?";
                $stmt = $mysqli_dev->prepare($query) for print_warning(__LINE__,"902","error preparing query: ");
                $stmt->bind_param("ss", $METHOD["email"],$METHOD["secret"]);
                api_select_excluding();
            }                
        }
        print_warning(__LINE__,"801","wrong login info or not an enabled developer (contact the administrator)");
        break;
      
        
        //GET
        
      
        // NEW - CREATE
        
        
        /*#>--- CREATE a new SESSION|new_session|name*;TODO||id|SESSION id<#*/
        case "new_session":
            //  /*TO DO*/
            checkFields($METHOD,["data"]);
            $table="session";  
            $prefix="SS"; //write it without _
            $length=6;//= MAX ID CAPACITY - PREFIX LENGTH - 1 (_)
            $obligedfields=["name"];
            $data=new_arrayifyData($METHOD["data"]);   
            new_ROW($data,$table,$prefix,$length,$obligedfields);
            break;
  
         /*#>--- CREATE a new SESSIONDATA|new_sessionData|session_id*;TODO||id|SESSIONDATA id<#*/
        case "new_sessionData":
           //   /*TO DO*/
            checkFields($METHOD,["data"]);
            $table="sessiondata";  
            $prefix="SD"; //write it without _
            $length=4;//= MAX ID CAPACITY - PREFIX LENGTH - 1 (_)
            $obligedfields=["session_id"];
            $data=new_arrayifyData($METHOD["data"]);  
            new_ROW($data,$table,$prefix,$length,$obligedfields);
            break;
     
        
         /*#>--- SAVE ACTIVITY DATA|save_actData|sessionId*;data*;TODO||id|SESSIONACTDATA id<#*/
         case "save_actData":
              
            checkFields($METHOD,["data"]); 
            checkFields($METHOD,["sessionId"]); 
            $data = new_arrayifyData($METHOD["data"]);
            $session = new_arrayifyData($METHOD["sessionId"]);
           
            foreach($data as $el => $elem){
                $source = $elem["source"];
                $payload = $elem["payload"];
                $creation = $elem["creation"];
                $autoincrement = $elem["autoincrement"];
                $ref=$elem["ref"];
                $table="sessionData";  
                $prefix="SD"; //write it without _
                $length=6;//= MAX ID CAPACITY - PREFIX LENGTH - 1 (_)
                $log = array('sessionAct_id' => $session, 'source' => $source, 'payload' => $payload,'creation' => $creation, 'autoincrement' => $autoincrement, 'ref' => $ref);
                $obligedfields=["sessionAct_id","source","payload","creation","autoincrement","ref"];  
                new_ROW($log,$table,$prefix,$length,$obligedfields,true);  
            };
            
            break;
        
        
        
       
        
     
    
        /// NOT MINE
        
       
        /*?#>GET an APP|getApp|id*||id;title;description;creation;category|APP<#?*/
        case "getApp":
              /*TO DO*/        
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["id"]);
            $query="SELECT * FROM app WHERE id=?";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("s", $data["id"]);
            api_select_excluding();               
            break;
        
        
        /*?>GET a RESOURCE (Download)|getResourceDownload|id*;type|Only 1 resource at once can be downloaded;if you define "type" the system will check if the type you are retrieving is the same as the one you asked for; A type can be: IMG (image), VID (video), SND (sound), TXT (text)|-|RAW FILE<--?*/
        case "getResourceDownload":
            //print_warning(__LINE__,"XXX","getResourceDownload has been deprecated");
                   
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["id","type"]);
            
            $file_name = $data["id"];
            $typet = $data["type"];
            $file_url = RESOURCE_FOLDER . $file_name . ".*";
            $result = glob ($file_url) or print_warning(__LINE__,"000","no file available");
            $server='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/";
            $url=$server.$result[0];
            list($type,$subtype,$extension)=getMediaType($url);
            ($type==$typet) or die("wrong_type");
            $file_name.=".".$extension;

            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"".$file_name."\""); 
            readfile($url);
            exit();           
            break;
        

        /*?#>GET a RESOURCE (Information)|getResourceInfo|id*|Only 1 resource info can be returned at once|id;type;subtype;title;tag;description;input;creation;extension;payload;size|RESOURCE<#?*/
        case "getResourceInfo":
              /*TO DO*/        
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["id"]);
            $query="SELECT * FROM resource WHERE id=?";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("s", $data["id"]);
            //api_select_excluding(["input"]); 
            api_select_excluding();               
            break;
        
       
        
        
        /*?#>GET a SESSION|getSession|id*||id;app_id;activity_id;server_id;client_id;start_configuration;live_configuration;notes;data;creation;dateStart;dateEnd|SESSION<#?*/
        case "getSession":
              /*TO DO*/        
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["id"]);
            $query="SELECT * FROM session WHERE id=?";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("s", $data["id"]);
            api_select_excluding();               
            break;
        
        //not implemented
        case "getSuite":
              /*TO DO*/        
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["id"]);
            $query="SELECT * FROM resource WHERE id=?";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("s", $data["id"]);
            api_select_excluding();               
            break;
        
        /*?#>GET an APP CATEGORY|getAppCategory|id*||id;title;description;image|CATEGORY<#?*/
        case "getAppCategory":
              /*TO DO*/        
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["id"]);
            $query="SELECT * FROM app_category WHERE id=?";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("s", $data["id"]);
            api_select_excluding();               
            break;
        
        /*?#>GET a USER|getUser|id*||id;role;email;firstname;familyname;enabled;locale;creation|USER<#?*/
        case "getUser":
              /*TO DO*/        
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["id"]);
            $query="SELECT * FROM user WHERE id=?";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("s", $data["id"]);
            api_select_excluding(["password","email"]);        
            break;
        
        
        case "getAllResourcesFromType":
              /*TO DO*/        
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["type"]);
            $query="SELECT * FROM resource WHERE type=?";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("s", $data["type"]);
            api_select_excluding();        
            break;
        
        /*?#>GET all ACTIVITIES from APP|getAllActivitiesFromApp|app_id*|A list can be returned;An empty list can be returned|id;app_id;title;description;creation;configuration;category|(list of) ACTIVITY<#?*/
        case "getAllActivitiesFromApp":
              /*TO DO*/        
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["app_id"]);
            $query="SELECT * FROM activity WHERE app_id=? ORDER by creation ASC";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("s", $data["app_id"]);
            api_select_excluding();        
            break;
        
        /*?#>GET all ACTIVITIES from CATEGORY|getAllActivitiesFromCategory|category*|A list can be returned;An empty list can be returned|id;app_id;title;description;creation;configuration;category|(list of) ACTIVITY<#?*/
        case "getAllActivitiesFromCategory":
              /*TO DO*/        
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["category"]);
            $query="SELECT * FROM activity WHERE category=? ORDER by creation ASC";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("s", $data["category"]);
            api_select_excluding();        
            break;
        
        //is it really needed?
        case "getAllSessionsFromApp":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["app_id"]);
            $query="SELECT * FROM session WHERE app_id=?";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("s", $data["app_id"]);
            api_select_excluding();        
            break;
        
        /*?#>GET all SESSIONS from APP and ACTIVITY|getAllSessionsFromAppAndActivity|app_id*;activity_id*|A list can be returned;An empty list can be returned |id;app_id;activity_id;server_id;client_id;start_configuration;live_configuration;notes;data;creation;dateStart;dateEnd|(list of) SESSION<#?*/
        case "getAllSessionsFromAppAndActivity":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["app_id","activity_id"]);
            $query="SELECT * FROM session WHERE app_id=? AND activity_id=?";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("ss", $data["app_id"], $data["activity_id"]);
            api_select_excluding();        
            break;
        
        /*?#>GET all SESSIONS from USER_SERVER and USER_CLIENT|getAllSessionsFromServerAndClient|server_id*;client_id*|A list can be returned;An empty list can be returned |id;app_id;activity_id;server_id;client_id;start_configuration;live_configuration;notes;data;creation;dateStart;dateEnd|(list of) SESSION<#?*/
        case "getAllSessionsFromServerAndClient":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["server_id","client_id"]);
            $query="SELECT * FROM session WHERE server_id=? AND client_id=?";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("ss", $data["server_id"], $data["client_id"]);
            api_select_excluding();        
            break;
        
        /*?#>GET all APPs from USER|getAllAppsFromUser|user_id*|A list can be returned;An empty list can be returned|id;title;description;creation;category|(list of) APP<#?*/
        case "getAllAppsFromUser":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["user_id"]);
            $query='SELECT A.* FROM app A INNER JOIN _app_user AU on A.id = AU.app_id INNER JOIN user U on AU.user_id = U.id WHERE U.id = ?';
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param('s', $data["user_id"]);
            api_select_excluding(); 
            break;
        
        //NOT SHOWN IN THE DOCUMENTATION
        case "getAllApps":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]);
            $query='SELECT * FROM app WHERE id<>?';
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $data["none"]=NONE;
            $stmt->bind_param('s', $data["none"]);
            api_select_excluding(); 
            break;
        
        //NOT SHOWN IN THE DOCUMENTATION
        case "getAllUsers":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]);
            $query='SELECT * FROM user WHERE id<>?';
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $data["none"]=NONE;
            $stmt->bind_param('s', $data["none"]);
            api_select_excluding(); 
            break;
        
        //NOT SHOWN IN THE DOCUMENTATION
        case "getAllUsersFromRole":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]);
            checkFields($data,["user_role"]);
            $query='SELECT * FROM user WHERE role=?';
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param('s', $data["user_role"]);
            api_select_excluding(); 
            break;
        
        /*?#>GET all ACTIVITIES from USER|getAllActivitiesFromUser|user_id*|A list can be returned;An empty list can be returned|id;app_id;title;description;creation;configuration;category|(list of) ACTIVITY<#?*/
        case "getAllActivitiesFromUser":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["user_id"]);
            $query='SELECT A.* FROM activity A INNER JOIN _activity_user AU on A.id = AU.activity_id INNER JOIN user U on AU.user_id = U.id WHERE U.id = ? ORDER by A.creation ASC';
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query: ".$mysqli->error);
            $stmt->bind_param('s', $data["user_id"]);
            api_select_excluding(); 
            break;
        
        /*?#>GET all CLIENTS from SERVER|getAllClientsFromServer|user_server_id*|A list can be returned;An empty list can be returned;|id;role;email;firstname;familyname;enabled;locale;creation|(list of) USER<#?*/
        case "getAllClientsFromServer":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["user_server_id"]);
            $query='SELECT U1.* FROM user U1 INNER JOIN _user_user UU on U1.id = UU.user_destination_id INNER JOIN user U2 on UU.user_source_id = U2.id WHERE U2.id = ?';
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param('s', $data["user_server_id"]);
            api_select_excluding(["password","email"]);
            break;
        
        /*?#>GET all APP CATEGORIES|getAllAppCategories||A list can be returned;An empty list can be returned |id;title;description;image|(list of) CATEGORY<#?*/
        case "getAllAppCategories":
              /*TO DO*/
            $query="SELECT * FROM app_category WHERE id<>?";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $data=array();
            $data["none"]=NONE;
            $stmt->bind_param("s", $data["none"]);
            api_select_excluding();        
            break;

        case "newUser":
              /*TO DO*/
            checkFields($METHOD,["data"]); //check if data object exists
            $table="user";  
            $prefix="U"; //without _
            $length=8;//= MAX ID CAPACITY - PREFIX LENGTH - 1 (_)
            $obligedfields=["email","password","role","firstname","familyname"];
            $data=new_arrayifyData($METHOD["data"]); 
            new_checkExistence($table,$data,"email"); //email univoca
            $data=new_initializeField($data,"creation",DEFAULT_DATETIME_NOW);
            $data=new_initializeField($data,"enabled","1");
            $data=new_initializeField($data,"locale","EN_US");  
            new_ROW($data,$table,$prefix,$length,$obligedfields);
            break;
        
        /*?#>LOG a SESSION|logSession|app_id*;activity_id*;server_id*;client_id*;start_configuration;live_configuration;notes;data*;dateStart*;dateEnd*||id|SESSION id<#?*/
        case "logSession":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $table="session";  
            $prefix="SS"; //without _
            $length=29;//= MAX ID CAPACITY - PREFIX LENGTH - 1 (_)
            $obligedfields=["app_id","activity_id","server_id","client_id"];
            $data=new_arrayifyData($METHOD["data"]); 
            $data=new_initializeField($data,"creation",DEFAULT_DATETIME_NOW);
            $data=new_initializeField($data,"dateStart",DEFAULT_DATETIME_EMPTY,false);
            $data=new_initializeField($data,"dateEnd",DEFAULT_DATETIME_EMPTY,false);
            new_ROW($data,$table,$prefix,$length,$obligedfields);
            break;
        
        /*?#>CREATE a new APP|newApp|title*;description;category||id|APP id<#?*/
        case "newApp":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $table="app";  
            $prefix="APP"; //without _
            $length=6;//= MAX ID CAPACITY - PREFIX LENGTH - 1 (_)
            $obligedfields=["title","category"];
            $data=new_arrayifyData($METHOD["data"]); 
            $data=new_initializeField($data,"creation",DEFAULT_DATETIME_NOW);
            new_ROW($data,$table,$prefix,$length,$obligedfields);
            break;
        
        
        
        
        
        /*?#>CONNECT ACTIVITY with USER|newRel_ActivityUser|user_id*;activity_id*||id|link id<#?*/
        case "newRel_ActivityUser":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $table="_activity_user";
            $obligedfields=["user_id","activity_id"];
            $data=new_arrayifyData($METHOD["data"]);
            checkIfIdExists("user",$data["user_id"]);
            checkIfIdExists("activity",$data["activity_id"]);
            new_ROW($data,$table,"",0,$obligedfields);
            $output["data"]["id"] = ["id"];
            break;
        
        /*?#>CONNECT APP with USER|newRel_AppUser|user_id*;app_id*||id|link id<#?*/
        case "newRel_AppUser":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $table="_app_user";
            $obligedfields=["user_id","app_id"];
            $data=new_arrayifyData($METHOD["data"]); 
            checkIfIdExists("user",$data["user_id"]);
            checkIfIdExists("app",$data["app_id"]);
            new_ROW($data,$table,"",0,$obligedfields);
            break;
        
        /*?#>CONNECT RESOURCE with USER|newRel_ResourceUser|user_id*;resource_id*||id|link id<#?*/
        case "newRel_ResourceUser":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $table="_resource_user";
            $obligedfields=["user_id","resource_id"];
            $data=new_arrayifyData($METHOD["data"]); 
            checkIfIdExists("user",$data["user_id"]);
            checkIfIdExists("resource",$data["resource_id"]);
            new_ROW($data,$table,"",0,$obligedfields);
            break;
        
        /*?#>CONNECT USER with USER|newRel_UserUser|user_source_id*;user_destination_id*|id||<#?*/
        case "newRel_UserUser":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $table="_user_user";
            $obligedfields=["user_source_id","user_destination_id"];
            $data=new_arrayifyData($METHOD["data"]); 
            checkIfIdExists("user",$data["user_source_id"]);
            checkIfIdExists("user",$data["user_destination_id"]);
            new_ROW($data,$table,"",0,$obligedfields);
            break;

        
        /*?#>EDIT an ACTIVITY|editActivity|id*;title;configuration;description;category||id|ACTIVITY id<#?*/
        case "editActivity":
              /*TO DO*/
            checkFields($METHOD,["data"]);
            $table="activity";
            $obligedfields=["id"];
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,$obligedfields);
            $query="SELECT * FROM activity WHERE id = '".$data["id"]."'"; 
            $result = $mysqli->query($query) or print_warning(__LINE__,"903","error executing query on ".$table);
            
            if ($activity = $result->fetch_assoc()){ 
                $data["title"]=editField($data,"title",$activity["title"]);
                $data["configuration"]=editField($data,"configuration",$activity["configuration"]);
                $data["description"]=editField($data,"description",$activity["description"]);
                $data["thumbnail"]=editField($data,"thumbnail",$activity["thumbnail"]);
                $data["category"]=editField($data,"category",$activity["category"]);
                $data["active"]=editField($data,"active",$activity["active"]);
                $data["baseurl"]=editField($data,"baseurl",$activity["baseurl"]);
                $data["type"]=editField($data,"type",$activity["type"]);
                
                $stmt = $mysqli->prepare("UPDATE activity SET title = ?, configuration = ?, description = ?, category = ?, thumbnail = ?, active = ?, baseurl = ?, type = ? WHERE id=?") or print_warning(__LINE__,"902","error preparing query: ".$mysqli->error);
                $stmt->bind_param("sssssssss", $data["title"], $data["configuration"], $data["description"], $data["category"], $data["thumbnail"], $data["active"],$data["baseurl"],$data["type"],$data["id"]);
                $stmt->execute() or print_warning(__LINE__,"903","error executing query on ".$table);
                $output=array();
                $output["data"] = array();
                $output["data"]["id"] = $data["id"];
                print_success($output,__LINE__);
        
            }else{
                print_warning(__LINE__,"000","activity id not found: ".$METHOD["id"]);
            }
            break;
        
        /*#> DELETE an ACTIVITY|deleteActivity|id*||id|ACTIVITY id<#*/
        case "deleteActivity":
              /*TO DO*/        
            checkFields($METHOD,["data"]);
            $data=new_arrayifyData($METHOD["data"]); 
            checkFields($data,["id"]);
            $query="DELETE FROM activity WHERE id=?";
            $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
            $stmt->bind_param("s", $data["id"]);
            $stmt->execute() or print_warning(__LINE__,"903","error executing query"); 
            $stmt->close(); 
            
            $output=array();
            $output["data"] = array();
            $output["data"]["id"] = $data["id"];
            print_success($output,__LINE__);
            break;
        
            /*$>LOGOUT a USER|logoutUser||||<$*/
        case "logout_userServer":
            destroyUserSession();
            break;
        
    default:
        print_warning(__LINE__,"201","no request named: ".$METHOD["request"]);
        break;
    
        
}
        
$mysqli->close();


function createUserSession($user){
    if(count($user["data"])>0){
        foreach($user["data"][0] as $key => $value){
            $_SESSION["user_".$key]=$value;
        }
    }else{
        print_warning(__LINE__,"000","the user does not exist");
    }

}

function getUserSession(){
    if(array_key_exists("user_id",$_SESSION)){
        $output=array();
        $output["data"] = $_SESSION;
        print_success($output,__LINE__);    
    }else{
        print_warning(__LINE__,"000","the session does not exist"); 
    }
}

function destroyUserSession(){
    // remove all session variables
    session_unset(); 
    // destroy the session 
    session_destroy();
    print_success("",__LINE__); 
}


function api_insert(){
    global $stmt;
    // Execute the statement.
    return($stmt->execute());
}

function api_select_excluding($exclude=array(),$return=false){
    global $stmt;
    if ($stmt->execute()){
        $stmt->store_result();
        $meta = $stmt->result_metadata();
        while ($field = $meta->fetch_field()) {
            $parameters[] = &$row[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $parameters);
        $x=array();
        $c=0;
        while ($stmt->fetch()) {
            $x[$c]=array();
            foreach ($row as $key => $val) {
                if(!in_array($key,$exclude)) $x[$c][$key] = $val;
            }
            $x[$c]=array_map("utf8_encode", $x[$c]);
            $c++;
        }
        $output=array();
        $output["data"] = $x;
        if(!$return) print_success($output,__LINE__);
        else return $output;
    } 
    else{
        print_warning(__LINE__,"903","error executing query");
    }
    print_warning(__LINE__,"0","undefined error");
}


function api_select_bool($stmt){
    if ($stmt->execute()){
        $stmt->store_result();
        if($stmt->num_rows()){
            return true;
        }
    }
    return false;
}

function checkIfIdExists($table,$id,$return=false){
    return checkIfFieldExists($table,$id,"id",$return);
}

function checkIfFieldExists($table,$fieldvalue,$field,$return=false){
    global $mysqli;
    $query="SELECT * FROM $table WHERE $field=?";
    $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
    $stmt->bind_param("s", $fieldvalue);
    if($return) return api_select_bool($stmt);
    else {
        if(!api_select_bool($stmt)) print_warning(__LINE__,"000","the ".$field." ".$fieldvalue." does not exists in ".$table);
    }
}


function checkFields($array,$fields,$return=false){    
    foreach($fields as $field)
    {
        if(!array_key_exists($field,$array) || $array[$field]==""){
           if($return){
               return false;
           }else{
               echo $field;
               print_warning(__LINE__,"102",$field);
           }
               
        }
    }
    if($return) return true;
}

function checkSession($role,$return=false){  
    if(!array_key_exists("user_id",$_SESSION) || !array_key_exists("user_role",$_SESSION) || $_SESSION["user_role"]>=$role){
        
       if($return){
            return false;
       }else{
            print_warning(__LINE__,"000","the session does not exist"); 
       }
    
    }
}

function getRequestHeaders() {
    $headers = array();
    //echo json_encode($_SERVER);
    foreach($_SERVER as $key => $value) {
        if (preg_match('/HTTP_AUTH_.*/', $key)) {
            $index=str_replace("HTTP_AUTH_","",$key);
            $headers[$index] = $value;
        }       
    }
    return ($headers);
}

function isJson($string) {
    if(is_array($string)) return false;
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

function generateRandomID($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[rand(0, $max-1)];
    }

    return $token;
}

function DynamicBindVariables($stmt, $params){
    if ($params != null)
    {
        // Generate the Type String (eg: 'issisd')
        $types = '';
        foreach($params as $param)
        {
            if(is_int($param)) {
                // Integer
                $types .= 'i';
            } elseif (is_float($param)) {
                // Double
                $types .= 'd';
            } elseif (is_string($param)) {
                // String
                $types .= 's';
            } else {
                // Blob and Unknown
                $types .= 'b';
            }
        }
  
        // Add the Type String as the first Parameter
        $bind_names[] = $types;
  
        // Loop thru the given Parameters
        for ($i=0; $i<count($params);$i++)
        {
            // Create a variable Name
            $bind_name = 'bind' . $i;
            // Add the Parameter to the variable Variable
            $$bind_name = $params[$i];
            // Associate the Variable as an Element in the Array
            $bind_names[] = &$$bind_name;
        }
         
        // Call the Function bind_param with dynamic Parameters
        call_user_func_array(array($stmt,'bind_param'), $bind_names);
    }
    return $stmt;
}

function new_matchColumnAndData($column,$data,$id=null){
    $coldata=array();
    $jsondata=$data;
    foreach($column as $col){
        if($col=="id"){
            array_push($coldata,$id);
        } else if(array_key_exists($col,$jsondata)) {
            array_push($coldata,$jsondata[$col]);
        } else {
            array_push($coldata,"");
        }
    }
    return $coldata;
}

function new_checkExistence($table,$data,$onwhichcolumn){
    global $mysqli;
    $query="SELECT * FROM ".$table." WHERE ".$onwhichcolumn."=?";
    $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
    $stmt->bind_param("s", $data["email"]);
    if(api_select_bool($stmt)) print_warning(__LINE__,"301","duplicated field value: ".$onwhichcolumn);;
}

function new_prepareInsertQuery($columns,$table){ 
    global $mysqli;
    $query="INSERT INTO ".$table." (";  
    $questionmarks="";
    for($i=0;$i<count($columns);$i++){
        $query.=$columns[$i].",";
        $questionmarks.="?,";
    }

    $query=substr($query, 0, -1);
    $questionmarks=substr($questionmarks, 0, -1);
    $query.=") VALUES (".$questionmarks.")";            
    $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query:". $mysqli->error);
    return $stmt;
}

function new_checkNewId($prefix,$table,$length){
    global $mysqli;
    do{
        $id=$prefix."_".generateRandomID($length);
        $query="SELECT * FROM ".$table." WHERE id=?";
        $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query");
        $stmt->bind_param("s", $id);
    }while(api_select_bool($stmt));
    return $id;
}

function new_checkInsertFields($table,$array_data,$array_obligedkeys=array()){
    global $mysqli;
    $columns=array();
    
    /*
    $query="SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`=?";
    $stmt = $mysqli->prepare($query) or print_warning(__LINE__,"902","error preparing query:". $mysqli->error);
    $stmt->bind_param("s", $table);
    $stmt->execute() or print_warning(__LINE__,"903","error executing query on ".$table);
    $stmt->bind_result($index);
    while ($stmt->fetch()) {
        array_push($columns,$index);
    }*/
    
    $sql = 'SHOW COLUMNS FROM '.$table;
    $res = $mysqli->query($sql);

    while($row = $res->fetch_assoc()){
        $columns[] = $row['Field'];
    }
    
    foreach($array_data as $key => $value){
        in_array($key,$columns) or print_warning(__LINE__,"203","the table ".$table." does not contain a field named: ".$key);
    }
    
    foreach($array_obligedkeys as $value){
        array_key_exists($value,$array_data) or print_warning(__LINE__,"204","the table ".$table." needs a data field named: ".$value);
    }
    
    return $columns;
    
}

function new_InitializeField($data,$field,$value,$force=true){
    
    if(is_array($data)){
        if(array_key_exists($field,$data) && $data[$field]=="") return $data; //$data[$field] already exists
        if($force==true){
            $data[$field]=$value;
        }
        
    }else{
        print_warning(__LINE__,"202");
    }
    return $data;
    
}

function editField($data,$field,$value){
    
    if(is_array($data)){
        if(array_key_exists($field,$data) && $data[$field]!=""){
            return $data[$field]; 
        } 
        else{
            return $value;
        }
    }else{
        print_warning(__LINE__,"202");
    }

}

function new_arrayifyData($data){
    
    /*if(safe_json_encode($data)==false){
        print_warning(__LINE__,"202","the *data* field is not a Json valid string - 1");
    }
    $data=safe_json_encode($data);*/
    if(!isJson($data)){
        $data=json_encode($data);
        if(!isJson($data)){
            print_warning(__LINE__,"202","the *data* field is not a Json valid string - 2");
        }
    }
    
    return json_decode($data,true);    
}

function new_ROW($data,$table,$prefix,$length,$obligedfields,$return=false){
    global $mysqli;
    if(isJson($data)) $data=json_encode($data);
    $columns=new_checkInsertFields($table,$data,$obligedfields);
    
    if($length!=0) 
    {
        $id=new_checkNewId($prefix,$table,$length); 
    }else {
        $id="TOOP";
    }
    
    if($table=="resource") 
    {
        if($data["type"]=="YT"){
            $data=new_initializeField($data,"payload","http://ludomi.i3lab.me/api/getResource.php?id=".youtube_id_from_url($data["input"])."&type=".$data["type"]);
        }else{
            $data=new_initializeField($data,"payload","http://ludomi.i3lab.me/api/getResource.php?id=".$id."&type=".$data["type"]);
        }
        
    }
    
    $stmt=new_prepareInsertQuery($columns,$table); 
    $columnwithdata=new_matchColumnAndData($columns,$data,$id);
    $stmt=DynamicBindVariables($stmt,$columnwithdata);                
    $stmt->execute() or print_warning(__LINE__,"903","error executing query:". $mysqli->error);
    $output=array();
    $output["data"] = $id;
    if($return) return $id;
    else print_success($output,__LINE__);
}

function addToHistory(){
    
    global $mysqli_dev;
    global $METHOD;
    
    $query="SELECT id FROM auth WHERE email=?";
    $stmt = $mysqli_dev->prepare($query) or print_warning(__LINE__,"902","error preparing query". $mysqli_dev->error);
    $stmt->bind_param("s", $METHOD["email"]);
    $stmt->execute() or print_warning(__LINE__,"903","error executing query on auth"); 
    $stmt->bind_result($dev_id);
    $stmt->fetch() or print_warning(__LINE__,"000","developer id not found".$dev_id);
    $stmt->close();
    
    $ip=mysqli_real_escape_string($mysqli_dev,$_SERVER['REMOTE_ADDR']);
    $payload=mysqli_real_escape_string($mysqli_dev,json_encode($METHOD));
    $header=mysqli_real_escape_string($mysqli_dev,json_encode($_SERVER));
    $request=mysqli_real_escape_string($mysqli_dev,$METHOD["request"]);
    $date=DEFAULT_DATETIME_NOW;
    
    $query="INSERT INTO `history` (`creation`,`dev_id`,`ip`,`request`,`payload`,`header`) VALUES (?,?,?,?,?,?)";
    //$query="INSERT INTO history (dev_id) VALUES (?)";
    $stmt = $mysqli_dev->prepare($query) or print_warning(__LINE__,"902","error preparing query:". $mysqli_dev->error);    
    $stmt->bind_param("ssssss", $date, $dev_id, $ip, $request, $payload, $header);  
    $stmt->execute() or print_warning(__LINE__,"903","error executing query on history"); 
    define("HISTORY_ID", $stmt->insert_id);
    $stmt->close();
    
}

/*
function safe_json_encode($value){
    if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
        $encoded = json_encode($value, JSON_PRETTY_PRINT);
    } else {
        $encoded = json_encode($value);
    }
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            return $encoded;
        case JSON_ERROR_DEPTH:
            return false;
            //return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
        case JSON_ERROR_STATE_MISMATCH:
            return false;
            //return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
        case JSON_ERROR_CTRL_CHAR:
            return false;
            //return 'Unexpected control character found';
        case JSON_ERROR_SYNTAX:
            return false;
            //return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
        case JSON_ERROR_UTF8:
            $clean = utf8ize($value);
            return safe_json_encode($clean);
        default:
            return false;
            //return 'Unknown error'; // or trigger_error() or throw new Exception()

    }
}

function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } else if (is_string ($mixed)) {
        return utf8_encode($mixed);
    }
    return $mixed;
}*/

//gestisce gli errori
function HandleError($errno, $errstr, $errfile, $errline, $errcontext){
    print_error("<b>Error:</b> [$errno] $errstr sulla linea $errline",$errline);
}

//stampa errore
function print_error($msg,$line){
 //   header('Content-Type: application/json');
    global $log;    
    global $DEBUG;
    global $current_server;
    error_log(date('j/n/Y G:i:s')." from ".$_SERVER['REMOTE_ADDR']."(".__FILE__."): ".$msg."<br/>", 3, $log["error"]);
    
    $output=array();
    $output["message"]=$msg;
    $output["status"]="error";
    $output["history_id"]= (defined('HISTORY_ID')) ? HISTORY_ID : "null";
    $output["datetime"]=DEFAULT_DATETIME_NOW;
    
    if($DEBUG) {
        $output["line"]=$line;
        $output["server"]=$current_server;
    }

    echo json_encode($output); 

    exit();
}

//stampa errore
function print_warning($line,$code,$msg=null){
   // header('Content-Type: application/json');
    global $log;    
    global $DEBUG;
    global $current_server;
    
    $output=array();
    
    $output["status"]="warning";
    $output["code"]=$code;
    $output["history_id"]= (defined('HISTORY_ID')) ? HISTORY_ID : "null";
    $output["datetime"]=DEFAULT_DATETIME_NOW;
    if($DEBUG) {
        $output["line"]=$line;
        $output["server"]=$current_server;
    }
    if($msg !== null) {
        $output["message"]=$msg;
    }
    echo json_encode($output);    
    exit();
}


//ritorna successo
function print_success($msg,$line=""){
    //header('Content-Type: application/json');
    global $DEBUG;
    global $current_server;
    $output=array();
    $output=$msg;
    $output["status"]="success";
    $output["history_id"]= (defined('HISTORY_ID')) ? HISTORY_ID : "null";
    $output["datetime"]=DEFAULT_DATETIME_NOW;
    if($DEBUG) {
        $output["line"]=$line;
        $output["server"]=$current_server;
    }
    echo json_encode($output);
    exit();
}

function print_success_modified($msg,$line=""){
    //header('Content-Type: application/json');
    global $DEBUG;
    global $current_server;
    $output=array();
    $output=$msg;
    $output["status"]="success";
    $output["history_id"]= (defined('HISTORY_ID')) ? HISTORY_ID : "null";
    $output["datetime"]=DEFAULT_DATETIME_NOW;
    if($DEBUG) {
        $output["line"]=$line;
        $output["server"]=$current_server;
    }
    echo json_encode($output);
}

function print_success_fast($msg,$line=""){
  //  header('Content-Type: application/json');
    global $DEBUG;
    global $current_server;
    $output=array();
    $output["msg"]=$msg;
    $output["status"]="success";
    $output["history_id"]= (defined('HISTORY_ID')) ? HISTORY_ID : "null";
    $output["datetime"]=DEFAULT_DATETIME_NOW;
    if($DEBUG) {
        $output["line"]=$line;
        $output["server"]=$current_server;
    }
    echo json_encode($output);
    exit();
}

function log_success($msg){

    global $log;
    error_log(date('j/n/Y G:i:s')." from ".$_SERVER['REMOTE_ADDR']."(".__FILE__."): ".$msg."<br/>", 3, $log["success"]);

}

function log_info($msg){
    global $log;

    error_log(date('j/n/Y G:i:s')." from ".$_SERVER['REMOTE_ADDR']."(".__FILE__."): ".$msg."<br/>", 3, $log["info"]);
}

?>