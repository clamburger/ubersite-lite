<?php
namespace Ubersite;

/**
 * A singleton used to hand out a connection to the MySQL server using PDO.
 * @return \PDO
 */
class DatabaseManager {

  /** @var \PDO */
  private static $dbh;

  public static function get() {
    // If the PDO object already exists, simply return it
    if (self::$dbh) {
      return self::$dbh;
    }

    $server = DB_HOST;
    $username = DB_USER;
    $password = DB_PASS;
    $database = DB_DATABASE;

    $dbh = new \PDO("mysql:host=$server; dbname=$database; charset=UTF8", $username, $password);
    $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
    $dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_BOTH);
    self::$dbh = $dbh;
    return $dbh;
  }

}
