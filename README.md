PowerGate
=========

PowerGate is a simple web application built for managing [PowerDNS](https://www.powerdns.com/) records, PowerGate is built using PHP on top of the [Laravel framework](http://www.laravel.com) and is designed to provide a RESTful API.

PowerGate is developed by Bobby Allen and released under the [GPLv3 license](LICENSE.md).

Installation
------------

The installation assumes that you have installed and configured PowerDNS using MySQL as the backend, if you have not yet done this please see a quick start guide in [Installing PowerDNS on Ubuntu Server](INSTALL.md).

To install the application download (or and in this example 'clone') the repository to your server, here is an example of how to install on a Ubuntu 12.04 LTS server:-

```shell
cd /var/www
git clone https://github.com/bobsta63/powergate.git ./
composer install
```

After installation of the application please locate the file and set your own API key in ```/var/www/app/config/Powergate.php```. If you did not follow the above linked installation documentation for Ubuntu Server you may also need to review and modify the DB connection settings of which can be found in ```/var/www/app/config/Database.php````.

PowerGate should now be ready to be used by third-party applications via. its REST API.