**INTRODUCTION**
----
  This JSON-based api is needed to communicate with Abilia DB.
  
  Abilia DB is now composed of the following entities (and soon more there will be):
    
  * **USER**: 
  * **ACTIVITY**
  * **APP**
  * **SESSION**
  * **RESOURCE**


To start with, you should ask for an account by sending an email to Mirko, the Administrator: phd.mirko.gelsomini@gmail.com. Soon he will send you: **username** and **secretkey** needed to ask for a **token** via this API.

A **token** is the main pass you'll need to use everytime you will query the api.

I've also created a POSTMAN link: https://www.getpostman.com/collections/ec519a0f01b8224a69a6
to try out the API. More info on POSTMAN: https://www.getpostman.com/

**DATA TYPES**
----
**USER**
* **Structure:**:
```json
    {
        "id": "« id »",
        "role": "« role »",
        "email": "« email »",
        "firstname": "« firstname »",
        "familyname": "« familyname »",
        "enabled": "« enabled »",
        "locale": "« locale »",
        "creation": "« creation »"
    }
```
* **Fields:**:
    * « enabled » = "1": user enabled, « enabled » = "0": user disabled
    * « locale » = XX_XX: all letters capitalized (http://quivi.sourceforge.net/languagecodes.html)
    * « creation »: date of account creation (0000-00-00 00:00:00 = Y-m-d H:i:s)
    * « role » = 0 superadmin, 1 supervisor, 2 server, 3 client, 4 observer


**ACTIVITY**
* **Structure:**:
```json
    {
        "id": "« id »",
        "app_id": "« app_id »",
        "title": "« title »",
        "description": "« description »",
        "creation": "« creation »",
        "configuration": "« configuration »",
        "category": "« category »",
        "active": "« active »",
        "type": "« type »",
        "thumbnail": "« thumbnail »",
        "baseurl": "« baseurl »"
    }
```
* **Fields:**:
    * « configuration »: the position you store the configuration of the activity (the settings)
    * « app_id » = APP_XXXXX: id of the main app (remember: an activity is an instance of an APP with its configuration)
    * « creation »: date of activity creation (0000-00-00 00:00:00 = Y-m-d H:i:s)
    * « category »: category of the Activity
    * « active »: is the activity active? true or false
    * « type »: for VR: 2d, 3d, 360
    * « thumbnail »: base64 encoded image
    * « baseurl »: url of the activity if it resides on another server
    
**APP**
* **Structure:**:
```json
    {
        "id": "« id »",
        "app_id": "« app_id »",
        "title": "« title »",
        "description": "« description »",
        "creation": "« creation »",
        "configuration": "« configuration »",
        "category": "« category »"
    }
```
* **Fields:**:
    * « configuration »: the position you store the configuration of the activity (the settings)
    * « app_id » = APP_XXXXX: id of the main app (remember: an activity is an instance of an APP with its configuration)
    * « creation »: date of activity creation (0000-00-00 00:00:00 = Y-m-d H:i:s)
    * « category »: category of the Activity


**SESSION**
* **Structure:**:
```json
    {
        "id": "« id »",
        "app_id": "« app_id »",
        "activity_id": "« activity_id »",
        "server_id": "« server_id »",
        "client_id": "« client_id »",
        "start_configuration": "« start_configuration »",
        "live_configuration": "« live_configuration »",
        "notes": "« notes »",
        "data": "« data »",
        "creation": "« creation »",
        "dateStart": "« dateStart »",
        "dateEnd": "« dateEnd »"
    }
```
* **Fields:**:
    * « app_id » = APP_XXXXX: id of the app
    * « app_id » = ACT_XXXXX: id of the activity
    * « start_configuration »: the position you store the start configuration of the session
    * « live_configuration »: the position you store the live configuration of the session (if needed)
    * « data »: the position you store the main data of the session (results)
    * « creation »: date of session creation (0000-00-00 00:00:00 = Y-m-d H:i:s)
    * « dateStart »: date and time of the start of the session (0000-00-00 00:00:00 = Y-m-d H:i:s)
    * « dateEnd »: date and time of the end of the session (0000-00-00 00:00:00 = Y-m-d H:i:s)

**RESOURCE**
* **Structure:**:
```json
    {
        "id": "« id »",
        "owner_id": "« owner_id »",
        "type": "« type »",
        "subtype": "« subtype »",
        "title": "« title »",
        "tag": "« tag »",
        "description": "« description »",
        "input": "« input »",
        "creation": "« creation »",
        "extension": "« extension »",
        "payload": "« payload »",
        "size": "« size »"
    }
```
* **Fields:**:
    * « owner_id » = id of the USER who owns the RESOURCE (NULL means that the RESOURCE is shared between all users)
    * « type » = ACT_XXXXX: id of the activity
    * « subtype »: the position you store the start configuration of the session
    * « tag »: comma separated tags (tag1, tag2, tag3...)
    * « input »: url of the uplaoded file - DO NOT USE THIS TO RETRIEVE THE FILE
    * « creation »: date of resource creation (0000-00-00 00:00:00 = Y-m-d H:i:s)
    * « payload »: url to download the RESOURCE or text - USE THIS TO RETRIEVE THE FILE
    * « size »: size in kilobyte

**GENERAL FORMATS**
    * DATE = dates are saved in this format: 0000-00-00 00:00:00 which is Y-m-d H:i:s (more info: http://php.net/manual/en/function.date.php)
    * LOCALE = languages (locales) are saved in this format: XX_XX. All letters are capitalized as shown here: http://quivi.sourceforge.net/languagecodes.html

**GENERAL INFO**
    * the asterisk * close to the value of a field means that the field is required otherwise the field is facultative

**COMMON ERRORS and WARNINGS**
----

**ATTENTION: IF YOU CANNOT SOLVE THE PROBLEM, PLEASE SEND AN E-EMAIL TO THE ADMINISTRATOR WITH THE ENTIRE FOLLOWING RESPONSE INCLUDED ** 

* **Sample Error/Warning Response:**

    ```json
    {
        "status": "error/warning",
        "code": "<a code from Error Codes>",
        "history_id": <id of the unique api call>,
        "datetime": "<date and time of the api call>",
        "line": "<possible line of the generated error>",
        "server": "<type of server used>",
        "message": "<other info>"
    }
    ```

* **Error/Warning Codes:**
    * **000**: general error, read comments
    * **101**: the *request* field is not defined -> You should insert a request name
    * **102**: a required field is not defined -> The field written in *message* is required
    * **201**: the *request* field is not recognized -> You probably mispelled the request name
    * **202**: the *data* field is not a Json valid string -> Check the validity of the json
    * **203**: a field in *data* is not a valid field -> Check the field name
    * **204**: an obliged field in *data* was not declared -> Assure you include that field
    * **301**: the *data* sent are already in the database, duplication error  
    * **801**: invalid token -> Request for a new token,  go to "How to obtain a TOKEN" section
    * **901**: db connection error -> Not your fault, contact the administrator
    * **902**: error preparing query -> Not your fault, contact the administrator
    * **903**: error executing query -> Not your fault, contact the administrator
    
**ACCEPTED FORMATS (form-data and raw)**
----
In JavaScript:

* **form-data:**
```javascript
var form = new FormData();
form.append("request", "« request »");
form.append("email", "« email »");
form.append("token", "« token »");
form.append("data", "« data »");
$.ajax({
        url: « api url »,
        method: "POST",
        processData: false,
        contentType: false,
        mimeType: "multipart/form-data",
        data: form,
        success: function (data) {
            console.log(JSON.parse(data));
        },
        error: function (request, error) {
            alert('An error occurred ' + error);
            console.log(request, error);
        }
});
```

* **raw:**
```javascript
var data = {
        "request": "« request »",
        "email": "« email »",
        "token": "« token »",
        "data": "« data »"
};
$.ajax({
        url: « api url »,
        type: "POST",
        processData: false,
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function (data) {
            console.log(data);
        },
        error: function (request, error) {
            alert('An error occurred ' + error);
            console.log(request, error);
        }
});
```

**Obtain a TOKEN**
----
  a TOKEN is the main pass to query the api.

* **URL** `http://ludomi.i3lab.me/api/`

* **Method:**

  `POST`
  
*  **URL Params**

   * `request=getApiToken[string]`
   * `email=<your-username>[string]`
   * `secret=<your-secretkey>[string]`

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** <br/>

    ```json
    {
        "data": [{
            "token": "<your-token>",
            "creationdate": "<date-of-request>"
        }],
        "status": "success"
    }
    ```


