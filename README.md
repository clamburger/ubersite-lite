# ÜberSite Lite

An internal website designed for use on [&Uuml;bertweak](http://ubertweak.org.au) and [related camps](http://crutech.org.au).

This is the lite version which comes with the bare minimum parts to make the questionnaire work.

## Requirements

* A web server with `mod_rewrite` (or compatible extension) enabled. Apache 2.2 or above is highly recommended.
* PHP 5.5 or higher
* PDO and the SQLite3 driver must be available.

## Installation

Very little setup is required; just drag the files into a directory of your choice and go. Note that at this time, from the web browser perspective, the files must be in the domain root. Whether you accomplish this by setting by a vhost or simply placing the files in the webserver root is up to you.

Once you've copied the files, you should be able to access the website in your browser. You'll have to go through a brief setup process which isn't overly complicated.

### That's not very intuitive

Welcome to &Uuml;berSite, enjoy your stay. We're working on making things easier, but it's a slow process.

## Things to be aware of

### Questionnaire

The questionnaire is a fickle beast, more prone to breaking than not. It's also in the middle of a large refactor, so it's neither here nor there at this point. My advice: wait for the documentation.

### Documentation

In general, things aren't very well documented (have you noticed?), so if something fails to "just work", that you may be in a spot of bother. Thankfully, the simpler things such as quotes, photos and polls have been pretty well tested, so you shouldn't have too many issues.
