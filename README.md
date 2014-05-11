![Build status](https://travis-ci.org/dionyziz/endofcodes.png?branch=master)

*End of Codes* is a game for programmers. You play the game by programming a bot which will play for you.

About the game
==============
The game is a 2D turn-based strategy game where all players play against all others. Every evening, a game takes place.
Each player is assigned some creatures on the 2D map, which they can move around. Creatures can attack enemy creatures.
Each creature attacked loses health points until it dies. The last player to remain alive wins.

When the game is ready, you can play on [endofcodes.com](http://endofcodes.com/).

End of Codes is written in HTML, CSS, Javascript, PHP, and MySQL.

Contributors
============
End of Codes was developed by:

 * Vitalis Salis <vitsalis@gmail.com>
 * Dimitris Lamprinos <pkakelas@gmail.com>
 * Dionysis Zindros <dionyziz@gmail.com>

If you're interested in contributing, just fork, fix a bug or build a feature, and pull request.

Installation
==========

### Requirements

 * PHP 5.5+
 * MySQL
 * Apache

PHP 5.5 is required because we use array-access-after-definition. PHP 5.4 is required because we use JSON notation
for arrays. PHP 5 is required because we use OOP5 features.

End of Codes has been tested under MySQL 5.5 and 5.6 and Apache 2.2, but it may work with other versions also.

### Environment Setup

* Fork the repository & Clone it on your machine. This will automatically create a new folder (in the current working directory) containing the repo.
* Create a new database called 'endofcodes'. 
* Import the **database/db_schema.sql** into the db through phpMyAdmin, or dump it through the command line. Like so: `mysql -u user -p pass endofcodes < absolute/path/to/dump/db_schema.sql` 

* Open **config/config.php** and set your 'user' and 'pass' for an existing user in the database.
Note: this applies just to the 'development' environment!
```
'development' => [
    'db' => [
        'host' => 'localhost',
        'user' => 'endofcodes',
        'pass' => 'sample_pass',
        'dbname' => 'endofcodes'
    ],
```

### Vhost Setup
* Vhost is a custom domain name on your local server, that points to a directory. This way you can identify and access your local projects through reasonable naming in the browser.
* If you're on Mac, w/ MAMP & Ports set to MySQL defaults, open **/private/etc/hosts** and add `127.0.0.1  endofgames.loc`. Like so:

```
127.0.0.1       localhost
127.0.0.1       endofgames.loc
```
Open **Applications/MAMP/conf/apache/httpd.conf** and uncomment the line `#Include /Applications/MAMP/conf/apache/extra/httpd-vhosts.conf`

Next open **Applications/MAMP/conf/apache/extra/httpd-vhosts.conf** and add this: 
```
<VirtualHost *:80>
    ServerName endofcodes.loc
    DocumentRoot absolute/path/to/folder/endofcodes
    <Directory absolute/path/to/folder/endofcodes>
    Options Indexes FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
    #Require all granted
    </Directory>
    ErrorLog endofcodes.log
    CustomLog endofcodes.log combined
</VirtualHost> 
```
Restart MAMP and open http://endofcodes.loc in the broswer.

* If you're on Linux [this](https://www.digitalocean.com/community/articles/how-to-set-up-apache-virtual-hosts-on-ubuntu-12-04-lts) tutorial explains a similar process.


Blog
====
You can read more about the development of the game on our [blog](http://blog.endofcodes.com).

License
=======
MIT. See the file [LICENSE](https://github.com/dionyziz/endofcodes/blob/master/LICENSE).
