# Übersite Lite

An internal website designed for use on [&Uuml;bertweak](http://ubertweak.org.au) and
[related camps](http://crutech.org.au).

This is the lite version which comes with the bare minimum functionality to make the questionnaire
work.

## Requirements

* PHP 5.5 or higher.
* PDO and the SQLite3 driver must be available.

## Installation

You will need a web server to run Übersite. Your options are Apache (the files will need to be in a
vhost or placed in the webserver root) or PHP's
[built-in web server](http://php.net/manual/en/features.commandline.webserver.php).

Once you've got the web server set up, open the site in the browser and you'll be greeted with a
very short setup process which will create the database and allow you to import users.

## The Questionnaire

There is [additional documentation](docs/questionnaire.md) available for the questionnaire setup. 
