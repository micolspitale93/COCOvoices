**How to get a USER**
----
  

* **URL**

  URLTODEFINE/api

* **Method:**

  `POST`
  
*  **URL Params**

   * `request=getUser[string]`
   * `username=<your-username>[string]`
   * `token=<your-token>[string]`
   * `id=<idUser>[string]`
   * `type=<user_type>[int]` 

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** <br/>

    ```json
    {
        "data": [{
            "id": "tc1",
            "email": "info@labilita.org",
            "name": "L'Abilit\u00e0 Onlus",
            "enabled": "1"
        }],
        "status": "success"
    }
    ```
    
**How to get all THERAPEUTIC CENTERS**
----
  
* **URL**

  URLTODEFINE/api

* **Method:**

  `POST`
  
*  **URL Params**

   * `request=getAllTherapeuticCenters[string]`
   * `username=<your-username>[string]`
   * `token=<your-token>[string]`

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** <br/>

    ```json
    {
        "data": [{
            "id": "tc1",
            "email": "info@labilita.org",
            "name": "L'Abilit\u00e0 Onlus",
            "enabled": "1"
        }, {
            "id": "tc2",
            "email": "info@sacrafamiglia.org",
            "name": "Sacra Famiglia",
            "enabled": "1"
        }],
        "status": "success"
    }
    ```

**How to get a THERAPIST**
----
  

* **URL**

  URLTODEFINE/api

* **Method:**

  `POST`
  
*  **URL Params**

   * `request=getTherapist[string]`
   * `username=<your-username>[string]`
   * `token=<your-token>[string]`
   * `id=<idTherapist>[string]`

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** <br/>

    ```json
    {
        "data": [{
            "id": "t1",
            "email": "terapista1@labilita.org",
            "firstName": "Marco",
            "lastName": "Maccini",
            "enabled": "1",
            "idTherapeuticCenter": "tc1"
        }],
        "status": "success"
    }
    ```

**How to get all THERAPISTS from a THERAPEUTIC CENTER**
----
  

* **URL**

  URLTODEFINE/api

* **Method:**

  `POST`
  
*  **URL Params**

   * `request=getAllTherapistsFromTherapeuticCenter[string]`
   * `username=<your-username>[string]`
   * `token=<your-token>[string]`
   * `id=<idTherapeuticCenter>[string]`

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** <br/>

    ```json
    {
        "data": [{
            "id": "t1",
            "email": "terapista1@labilita.org",
            "firstName": "Marco",
            "lastName": "Maccini",
            "enabled": "1",
            "idTherapeuticCenter": "tc1"
        }, {
            "id": "t2",
            "email": "terapista2@labilita.org",
            "firstName": "Susanna",
            "lastName": "Susini",
            "enabled": "1",
            "idTherapeuticCenter": "tc1"
        }],
        "status": "success"
    }
    ```

**How to get a CHILD**
----
  
* **URL**

  URLTODEFINE/api

* **Method:**

  `POST`
  
*  **URL Params**

   * `request=getTherapeuticCenter[string]`
   * `username=<your-username>[string]`
   * `token=<your-token>[string]`
   * `id=<idChild>[string]`

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** <br/>

    ```json
    {
        "data": [{
            "id": "c1",
            "firstName": "Andrea",
            "lastName": "Aliu",
            "idTherapeuticCenter": "tc1"
        }],
        "status": "success"
    }
    ```

**How to get all CHILDREN from a THERAPEUTIC CENTER**
----
  
* **URL**

  URLTODEFINE/api

* **Method:**

  `POST`
  
*  **URL Params**

   * `request=getAllChildrenFromTherapeuticCenter[string]`
   * `username=<your-username>[string]`
   * `token=<your-token>[string]`
   * `id=<idTherapeuticCenter>[string]`

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** <br/>

    ```json
    {
        "data": [{
            "id": "c1",
            "firstName": "Andrea",
            "lastName": "Aliu",
            "idTherapeuticCenter": "tc1"
        }, {
            "id": "c2",
            "firstName": "Carletto",
            "lastName": "Comaschini",
            "idTherapeuticCenter": "tc1"
        }, {
            "id": "c3",
            "firstName": "Daniela",
            "lastName": "Donnoli",
            "idTherapeuticCenter": "tc1"
        }, {
            "id": "c4",
            "firstName": "Eriberto",
            "lastName": "Escimi",
            "idTherapeuticCenter": "tc1"
        }],
        "status": "success"
    }
    ```
  

**How to get all CHILDREN from a PARENT**
----
  

* **URL**

  URLTODEFINE/api

* **Method:**

  `POST`
  
*  **URL Params**

   * `request=getAllChildrenFromParent[string]`
   * `username=<your-username>[string]`
   * `token=<your-token>[string]`
   * `id=<idParent>[string]`

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** <br/>

    ```json
    {
        "data": [{
            "id": "c1",
            "firstName": "Andrea",
            "lastName": "Aliu",
            "idTherapeuticCenter": "tc1"
        }, {
            "id": "c2",
            "firstName": "Carletto",
            "lastName": "Comaschini",
            "idTherapeuticCenter": "tc1"
        }],
        "status": "success"
    }
    ```
    
**How to get all CHILDREN from a THERAPIST**
----
  

* **URL**

  URLTODEFINE/api

* **Method:**

  `POST`
  
*  **URL Params**

   * `request=getAllChildrenFromTherapist[string]`
   * `username=<your-username>[string]`
   * `token=<your-token>[string]`
   * `id=<idTherapist>[string]`

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** <br/>

    ```json
    {
        "data": [{
            "id": "c1",
            "firstName": "Andrea",
            "lastName": "Aliu",
            "idTherapeuticCenter": "tc1"
        }, {
            "id": "c3",
            "firstName": "Daniela",
            "lastName": "Donnoli",
            "idTherapeuticCenter": "tc1"
        }],
        "status": "success"
    }
    ```