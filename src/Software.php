<?php
namespace Ubersite;

class Software {
  public static $name = "ÜberSite";
  public static $version = "2.1.0";
  public static $codename = "Fenix";

  public function getFullName() {
    return self::$name . " " . self::$codename;
  }

  public function getName() {
    return self::$name;
  }

  public function getVersion() {
    return self::$version;
  }

  public function getCodename() {
    return self::$codename;
  }
}
