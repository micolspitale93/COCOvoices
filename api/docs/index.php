<html>

<head>
    <link rel="stylesheet" type="text/css" href="assets/style.css">
    <meta charset="UTF-8">

    <title>Documentation</title>

</head>

<body>
    
    <div id="toc" class="right"></div>
    
     <div id="searchBox"><input placeholder="Search here" id="searchField" type="search"></div>
    
    <div id="wrapper">
        
   

    <?php
    include "assets/Parsedown.php";
    $Parsedown = new Parsedown();
    
    $URL="http://ludomi.i3lab.me/api/";
    $METHOD="POST";
    
    $php = file_get_contents('../index.php');
    //echo $php;
    $matches=getInbetweenStrings($php);
    //var_dump($matches);

    $md = file_get_contents('assets/intro.md');

    echo $Parsedown->text($md);
    
    $text="";
        
    foreach($matches as $match){
        $m=explode("|",$match);
        
        $fields=explode(";",c($m,2));
        $last_index = count($fields)-1;
        $data=""; 
        $comma=",";
        $c=0;
        
        foreach($fields as $field){
            if ($c == $last_index) $comma="";
            if (strpos($field, '*') !== false){
$field=str_replace("*","",$field);
$data .= <<<TXT
"$field": "« $field* »"$comma

TXT;
            }else{
$data .= <<<TXT
"$field": "« $field »"$comma

TXT;
            }

            $c++;
        }
        
        
        $information=""; 
        if(c($m,3)!=""){
$infos=explode(";",c($m,3));
$information .= <<<TXT
* **Other info:**

TXT;
        foreach($infos as $info){
$information .= <<<TXT
    * $info

TXT;
        }
        }
        
        
        $resps=explode(";",c($m,4));
        $last_index = count($resps)-1;
        $response=""; 
        $comma=",";
        $c=0;
        foreach($resps as $resp){
            if ($c == $last_index) $comma="";
$response .= <<<TXT
            "$resp": "« $resp »"$comma

TXT;
            $c++;
        }
        
        $datatype=c($m,5);
        
        if($resp!="-"){
        
$text .= <<<TXT
**$m[0]**
----
* **URL to call:** `$URL`
* **Method:** `$METHOD`
* **Accepted formats:** form-data or raw (valid json)
*  **URL Params:**
    * `request=$m[1][string]`
    * `email=« your-email »[string]`
    * `token=« your-token »[string]`
    * data=
    ```json
    {
$data}
    ```
$information
* **Success Response:**
    ```json
    {
        "data": [{
$response        }],
        "status": "success"
    }

    ```
* **Returned DATA Type:** $datatype

    
TXT;
            }else{
            
$text .= <<<TXT
**$m[0]**
----
* **URL to call:** `$URL`
* **Method:** `$METHOD`
* **Accepted formats:** form-data or raw (valid json)
*  **URL Params:**
    * `request=$m[1][string]`
    * `email=« your-email »[string]`
    * `token=« your-token »[string]`
    * data=
    ```json
    {
$data}
    ```
* **Other info:**
$information

* **Returned DATA Type:** $datatype

    
TXT;
            
        }
    }    
    echo $Parsedown->text($text);

    function getInbetweenStrings($str){
        //var_dump($str);
        $matches = array();
        /*$re = '/(?<=#>)(.*)(?=<#)/';
        preg_match_all($re, $str, $matches);
        return $matches[1];*/
        $str1=explode("/*#>",$str);
        for($i=0;$i<count($str1);$i++){
            $str2=explode("<#*/",$str1[$i]);
            $matches[]=$str2[0];
        }
        array_shift($matches);
        //var_dump($matches);
        return $matches;
        
    }

        
    function c($array,$key){
        if(array_key_exists($key,$array))
           return $array[$key];
        else
           return "";
    }

    ?>

    </div>

    <script type="application/javascript" src="assets/jquery-3.1.1.min.js"></script>
    <script type="application/javascript" src="assets/toc.min.js"></script>
<script>
$('#toc').toc({
    'selectors': 'h1,h2,h3', //elements to use as headings
    'container': '#wrapper', //element to find all selectors in
    'smoothScrolling': true, //enable or disable smooth scrolling on click
    'prefix': 'toc', //prefix for anchor tags and class names
    'onHighlight': function(el) {}, //called when a new section is highlighted 
    'highlightOnScroll': true, //add class to heading that is currently in focus
    'highlightOffset': 100, //offset to trigger the next headline
    'anchorName': function(i, heading, prefix) { //custom function for anchor name
        return prefix+i;
    },
    'headerText': function(i, heading, $heading) { //custom function building the header-item text
        return $heading.text();
    },
    'itemClass': function(i, heading, $heading, prefix) { // custom function for item class
      return $heading[0].tagName.toLowerCase();
    }
});
    </script>
    <script type="application/javascript" src="assets/main.js"></script>
</body>


</html>