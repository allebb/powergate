PowerGate
=========

PowerGate is a simple web application built for managing [PowerDNS](https://www.powerdns.com/) records, PowerGate is built using PHP on top of the [Laravel framework](http://www.laravel.com) and is designed to provide a RESTful API.

PowerGate is developed by [Bobby Allen](http://bobbyallen.me) and released under the [GPLv3 license](LICENSE.md).

Installation
------------

The installation assumes that you have installed and configured [PowerDNS](https://www.powerdns.com/) using [MySQL](http://www.mysql.com) as the backend, if you have not yet done this please see a quick start guide in [Installing PowerDNS on Ubuntu Server](INSTALL.md).

To install the application download (or and in this example 'clone') the repository to your server, here is an example of how to install on a Ubuntu 12.04 LTS server:-

```shell
cd /var/www
git clone https://github.com/bobsta63/powergate.git ./
composer install
```

After installation of the application please locate the file and set your own API key in `/var/www/app/config/Powergate.php`. If you did not follow the above linked installation documentation for Ubuntu Server you may also need to review and modify the DB connection settings of which can be found in `/var/www/app/config/Database.php`.

PowerGate should now be ready to be used by third-party applications via. its REST API.

API resources
-------------

Each of the API endpoints provide the ability to list (all), show (details of a single record), create new, update and destroy.

The API uses HTTP request types in order to maniupate the API accordingly.

### Domains

#### Listing all domains

The API enables you to list all of the currently listed domains that you have configured on your server by using the following request:

```
GET https://api.yourdnsserver.com/domains
```

An example of the response is as follows:

```json
{
    "errors": false,
    "domains": [
        {
            "id": 1,
            "name": "example.com",
            "master": null,
            "last_check": null,
            "type": "NATIVE",
            "notified_serial": null,
            "account": null
        },
        {
            "id": 2,
            "name": "mydomain.com",
            "master": null,
            "last_check": null,
            "type": "MASTER",
            "notified_serial": null,
            "account": ""
        }
    ]
}
```

#### Returning a single domain record

Returning a domain is the same as requesting the full list of domains, except you are required to specify the domain ID in the request URI, so if we wanted to return a single record (*In this case a domain with an ID of 1*) we'd request it like so:

```
GET https://api.yourdnsserver.com/domains/1
```

The expected response is as follows:

```json
{
    "errors": false,
    "domain": {
        "id": 1,
        "name": "example.com",
        "master": null,
        "last_check": null,
        "type": "NATIVE",
        "notified_serial": null,
        "account": null
    }
}
```

#### Returning a single domain and all associated records

To retrieve the domain and all of the related records for that domain, a simple **GET** request can be made as follows:

```
GET https://api.yourdnsserver.com/domains/1/records
```

By specifying the domain ID and then requesting `records` as the end of the URI the API will now respond with the details of the domain as well as a listing of all records for that domain like so:

```json
{
    "errors": false,
    "domain": {
        "id": 1,
        "name": "example.com",
        "master": null,
        "last_check": null,
        "type": "NATIVE",
        "notified_serial": null,
        "account": null,
        "records": [
            {
                "id": 1,
                "domain_id": 1,
                "name": "example.com",
                "type": "SOA",
                "content": "localhost ahu@ds9a.nl 1",
                "ttl": 86400,
                "prio": null,
                "change_date": null
            },
            {
                "id": 2,
                "domain_id": 1,
                "name": "example.com",
                "type": "NS",
                "content": "dns-us1.powerdns.net",
                "ttl": 86400,
                "prio": null,
                "change_date": null
            },
            ...
            {
                "id": 14,
                "domain_id": 1,
                "name": "server2.example.com",
                "type": "A",
                "content": "126.22.98.99",
                "ttl": 3600,
                "prio": null,
                "change_date": 1392546342
            }
        ]
    }
}
```

If you wish to return all records regardless of their association with a particular domain, please see the section named *Listing all records* further on.

#### Creating a new domain

To create a new domain, we must make a **POST** request specify some paramters, the parameters that we need to specify are as follows:

* **name** - Required, unique
* **type** - Domain type, valid entries are (MASTER, SLAVE, NATIVE)

You can additionally set the following parameters too but they are not required by default:

* **master** - Optionally you can specify a master DNS server, this is an IP address NOT an FQDN.
* **account** - Optionally you can assign an account name, this is the 'owner' this is reserved for future upgrades.

Here is an example request to create a new domain:

```
POST https://api.yourdnsserver.com/domains 
PARAMS name=mydomain.com&type=MASTER
```

Upon successful creation, the API will respond back with a **201** HTTP response as follows:

```json
{
    "errors": false,
    "domain": {
        "name": "mydomain.com",
        "master": null,
        "type": "MASTER",
        "account": null,
        "id": 2
	}
}
```

#### Updating an existing domain

Using the **PUT** or **PATCH** HTTP methods an existing record can be updated using the API, if we wanted to update our new '*mydomain.com*' domain and change it from a 'MASTER' type to 'SLAVE' we could do it like so:

```
PATCH https://api.yourdnsserver.com/domains/2 
PARAMS name=mydomain.com&type=SLAVE
```

Upon successful update of the resource, the a **200** response should be recieved detailing the new resource as shown here:

```json
{
    "errors": false,
    "domain": {
        "id": 2,
        "name": "mydomain.com",
        "master": null,
        "last_check": null,
        "type": "SLAVE",
        "notified_serial": null,
        "account": null
    }
}
```

#### Deleting a domain

To delete a domain, it's as simple as sending a **DELETE** request specifing the domain ID, for example:

```DELETE https://api.yourdnsserver.com/domains/2```

Upon successful deletion of the domain, the following **200** response will be returned:

```json
{
    "errors": false,
    "message": "Deleted successfully"
}
```

### Records

#### Listing all records

The API enables you to list all of the currently listed records that you have configured on your server by using the following request:

```
GET https://api.yourdnsserver.com/records
```

An example of the response is as follows:

```json
{
    "errors": false,
    "records": [
        {
            "id": 1,
            "domain_id": 1,
            "name": "example.com",
            "type": "SOA",
            "content": "ns1.example.com ballen@bobbyallen.me 20140216",
            "ttl": 86400,
            "prio": null,
            "change_date": null
        },
        {
            "id": 2,
            "domain_id": 1,
            "name": "example.com",
            "type": "NS",
            "content": "dns-us1.powerdns.net",
            "ttl": 86400,
            "prio": null,
            "change_date": null
        },
        {
            "id": 3,
            "domain_id": 1,
            "name": "example.com",
            "type": "NS",
            "content": "dns-eu1.powerdns.net",
            "ttl": 86400,
            "prio": null,
            "change_date": null
        },
        ....
        {
            "id": 11,
            "domain_id": 1,
            "name": "example.com",
            "type": "NS",
            "content": "dns-us1.powerdns.net",
            "ttl": 86400,
            "prio": null,
            "change_date": 1392498361
        }
    ]
}
```

If you wish to only return a list of records for a particular domain only please see the section named *Returning a single domain and all associated records* as mentioned earlier.

#### Returning a single record

Returning a record is the same as requesting the full list of record, except you are required to specify the record ID in the request URI, so if we wanted to return a single record (*In this case record with an ID of 1*) we'd request it like so:

```
GET https://api.yourdnsserver.com/records/1
```

The expected response is as follows:

```json
{
    "errors": false,
    "record": {
        "id": 1,
        "domain_id": 1,
        "name": "example.com",
        "type": "SOA",
        "content": "ns1.example.com ballen@bobbyallen.me 20140216",
        "ttl": 86400,
        "prio": null,
        "change_date": null
    }
}
```

#### Creating a new record

To create a new record, we must make a **POST** request specify some paramters, the parameters that we need to specify are as follows:

* **domain_id** - Required, Numeric
* **name** - Required
* **type** - Record type, valid entries are (A, AAAA, CNAME, HINFO, MX, NAPTR, NS, PTR, SOA, SPF, SRV, SSHFP, TXT and RP)
* **content** - Required
* **ttl** - Required, Numeric

You can additionally set the following parameters too but they are not required by default:

* **prio** - Priory rank of records such as MX records, defaults to null otherwise.

Here is an example request to create a new domain:

```
POST https://api.yourdnsserver.com/records 
PARAMS domain_id=1&name=server2.example.com&type=A&content=126.22.98.2&ttl=3600
```

Upon successful creation, the API will respond back with a **201** HTTP response as follows:

```json
{
    "errors": false,
    "record": {
        "domain_id": "1",
        "name": "server2.example.com",
        "type": "A",
        "content": "126.22.98.2",
        "ttl": "3600",
        "prio": null,
        "change_date": 1392545724,
        "id": 12
    }
}
```

	Please note: In order for the Slave DNS servers to update (as per the 'Superslave' configuration mentioned in the '[Installation notes](INSTALL.md)'), you are required to increment the SOA serial for the domain, this is something that I feel should be done by the API client as you may wish to bulk change/create records and only then increment the SOA serial to reduce serial incementations and reduce API calls. Therefore the API server will not do this automatically!

#### Updating an existing record

Using the **PUT** or **PATCH** HTTP methods an existing record can be updated using the API, if we wanted to update our new '*server2.example.com*' record and change it using an IP address for the A record from '126.22.98.2' to '126.22.98.99':

```
PATCH https://api.yourdnsserver.com/domains/2 
PARAMS domain_id=1&name=server2.example.com&type=A&content=126.22.98.99&ttl=3600
```

Upon successful update of the resource, the a **200** response should be recieved detailing the new resource as shown here:

```json
{
    "errors": false,
    "record": {
        "domain_id": "1",
        "name": "server2.example.com",
        "type": "A",
        "content": "126.22.98.99",
        "ttl": "3600",
        "prio": null,
        "change_date": 1392546308,
        "id": 12
    }
}
```

#### Deleting a record

To delete a record, it's as simple as sending a **DELETE** request specifing the record ID, for example:

```DELETE https://api.yourdnsserver.com/records/12```

Upon successful deletion of the domain, the following **200** response will be returned:

```json
{
    "errors": false,
    "message": "Deleted successfully"
}
```


### Standard API Error responses

The API intends to provide a simple set of standardised error responses, each of the API methods provide the following standardised error responses in certain situations:

#### Unauthorised

To access the API, you must provide HTTP authentication headers, the username field must match the `user` value and the password field must match the `key` value in the `app/config/Powergate.php` configuration file, if these do not match and/or the authorisation headers are not sent to the API server by your client then you will recieve a **401** response like so:

```json
{
    "error": true,
    "message": "Unauthorised request"
}
```

#### Invalid API requests (URI's etc)

If an invalid URI is requested or a client attempts to **POST** to a **GET** only endpoint for example, the API will respond with a **404** response with the following message:

```json
{
    "error": "true",
    "message": "Invalid request, see https://github.com/bobsta63/powergate/blob/master/README.md for API endpoint URI's"
}
```

This response differs from the Not found message as documented below in the fact that the API does not know how to handle the request as opposed to simply not able to find a resource object.

#### Not found

When a requested resource can not be found, such as when requesting a specific resource or attempting to update a resource of which does not appear to have a corresponding ID in the database, the following **404** response is generated:

```json
{
    "errors": true,
    "message": "Not found"
}
```

#### Validation errors

When a request can not be completed due to invalid data, the API will respond with a **400** status code (*Bad request*) and the following JSON data will be returned:

```json
{
    "errors": true,
    "message": "Data validation failed"
}
```

#### Server error

For all other errors a standard **500** status response will be thrown for situatons where we did not track the issue to a particular event, in this situation the following response will be return:

```json
{
    "errors": true,
    "message": "Server error"
}
```