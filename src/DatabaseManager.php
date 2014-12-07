<?php
namespace Ubersite;

/**
 * A singleton used to hand out a connection to the SQLite database using PDO.
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

    // PDO's sqlite driver is very particular, so make sure we're in the directory with the database.
    $workingDirectory = getcwd();
    chdir(__DIR__ . '/../config');

    $dbh = new \PDO('sqlite:database.db');
    $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
    $dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_BOTH);
    self::$dbh = $dbh;

    chdir($workingDirectory);

    return $dbh;
  }

}
