<?php
namespace Ubersite;

class Software
{
    public static $name = "Übersite";
    public static $version = "2.2.0";
    public static $codename = "Ghost";

    public function getFullName()
    {
        return self::$name . " " . self::$codename;
    }

    public function getName()
    {
        return self::$name;
    }

    public function getVersion()
    {
        return self::$version;
    }

    public function getCodename()
    {
        return self::$codename;
    }
}
