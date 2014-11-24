<?php
namespace Ubersite;

class Software {
  static $name = "ÜberSite";
  static $version = "2.1.0";
  static $codename = "Fenix";

  static function getFullName() {
    return Software::$name . " " . Software::$codename;
  }
}
