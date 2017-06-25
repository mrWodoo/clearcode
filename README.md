clearcode
=========

PHP7.0 is required to run this project.

Configure proper virtual host
```
<VirtualHost *:80>
        ServerName cc.dev
        ServerAdmin webmaster@localhost

        DocumentRoot /var/www/public/clearcode/web/

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```
#
Run composer from project's root directory

```composer install```

Configure ./behat.yml `base_url`

#
Simple REST API documentation
==============

Fetching every person in system

```GET /```

```GET /person```

```GET /person/```

#
Fetching specific person by id

```
GET /person/{id}
Returns code 200 on success
Returns code 404 when person not found
Response body: JSON with person data
```

#
Removing specific person from system

```
DELETE /person/delete/{id}
Returns code 200 on success
Returns code 404 when person not found
```

#
Updating person

```
PATCH /person/update/{id}
Returns code 200 on success
Returns code 404 when person not found
Returns code 400 when invalid input is sent

Request body:
{
"firstName" : "Denis",
}

```
Will set firstName to "Denis"

#
Creating person

```
PUT /person/create
Returns code 200 on success
Returns code 400 when invalid input is sent

Request body: see "More advanced example"

Response body: JSON with person data
```

List of fields you can manage
====
If person has no agreement then new will be created.

Same thing with adresses, if address with given type does not exist then it will be created.

* firstName (length between 3 and 32)
* lastName (length between 3 and 32)
* phone (length up to 15 chars)
* agreement
    * number (length between 32 and 64)
    * signingDate (Y-m-d H:i:s)
* adresses (max 3, one of each type)
    * billing|home|shipping
        * address (up to 255 chars)
        * city (between 3 and 100 chars)
    * billing|home|shipping
        * ...
        
More advanced example

``` PATCH /person/{id}
{
	"firstName": "John",
	"lastName": "Doe",
	"agreement": {
		"number": "201706241352592017062413525920170624135259",
		"signingDate": "2017-02-02 13:37:00"
	},
	"addresses": {
		"home": {
			"city": "Gliwice",
			"address": "ul. Undefined 404/500"
		}
	}
}
```

