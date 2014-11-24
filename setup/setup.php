<?php
require '../vendor/autoload.php';

use Ubersite\bTemplate;
use Ubersite\Software;

error_reporting(E_ALL);

if (file_exists("../config/config/config.php") && !isset($_GET['override'])) {
  header("Location: ../");
}

$tpl = new bTemplate();

$tpl->set("version", Software::$version);
$tpl->set("codename", Software::$codename);
$tpl->set("software", Software::$name);

$criticalError = false;
$checks = array_fill_keys(array("configWritable", "mysql"), true);

if (PHP_VERSION_ID < 50500) {
  $checks["php"] = "<span class='label important'>".PHP_VERSION."</span>";
  $criticalError = true;
} else {
  $checks["php"] = "<span class='label success'>".PHP_VERSION."</span>";
}

# Check required directories

$directories = array("config", "config/config");

foreach ($directories as $dirName) {
  if (file_exists("../$dirName")) {
    # Directory exists, check if it's writable
    if (!is_writable("../$dirName")) {
      $checks["configWritable"] = "the <tt>$dirName</tt> directory needs to be writable. Installation cannot continue.";
      $criticalError = true;
      break;
    }
  } else {
    # Doesn't exist: try and create it
    if (!@mkdir("../$dirName")) {
      $checks["configWritable"] = "the <tt>$dirName</tt> directory could not be created. Installation cannot continue.";
      $criticalError = true;
      break;
    }
    chmod("../$dirName", 0777);
  }
}

if (!in_array('mysql', PDO::getAvailableDrivers())) {
  $checks["mysql"] = "you may need to install <tt>php5-mysql</tt>. Installation cannot continue.";
  $criticalError = true;
}

foreach ($checks as $key => $value) {
  if ($key == "php") {
    continue;
  }
  if ($value === true) {
    $checks[$key] = "<span class='label success'>yes</span>";
  } else {
    $checks[$key] = "<span class='label important'>no</span> - $value";
  }
}

$tpl->set("checks", $checks);
$tpl->set("error", $criticalError);

echo $tpl->fetch("setup.tpl");
