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

Returning a domain is the same as requesting the full list of domains, except you are required to specify the domain ID in the request URI, so if we wanted to return a single record (*In this case record with an ID of 1*) we'd request it like so:

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

TBC

### Supermasters

TBC

### Standard API Error responses

The API intends to provide a simple set of standardised error responses, each of the API methods provide the following standardised error responses in certain situations:

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