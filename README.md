# Übersite Lite

An internal website designed for use on [&Uuml;bertweak](http://ubertweak.org.au) and
[related camps](http://crutech.org.au).

This is the lite version which comes with the bare minimum functionality to make the questionnaire
work.

## Requirements

* PHP 5.5 or higher.
* PDO and the SQLite3 driver must be available.
* [Composer](https://getcomposer.org/download/)

## Installation

You will need a web server to run Übersite. Your options are Apache (the files will need to be in a
vhost or placed in the webserver root) or PHP's
[built-in web server](http://php.net/manual/en/features.commandline.webserver.php).

Run `composer install` once in order to download the dependencies that Übersite needs.

Once you've got the web server set up, open the site in the browser and you'll be greeted with a
very short setup process which will create the database and allow you to import users.


## Docker

Run the following to build the container:

  $ docker build -t survey .
  $ docker run survey:latest

and then access your local machine on port 80. Once you have assigned the site name and imported
users, you should run the following to import a templated questionaire:

  $ docker exec <containername> /usr/bin/sqlite3 /var/www/html/config/database.db < ./questionaire_template.sql


