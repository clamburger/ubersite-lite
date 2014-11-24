<?php
require '../vendor/autoload.php';

use Ubersite\Software;

error_reporting(E_ALL);

if (file_exists("../config/config.php") && !isset($_GET['override'])) {
  header("Location: ../");
}

$loader = new Twig_Loader_Filesystem('.');
$twig = new Twig_Environment($loader);

$twig->addGlobal("version", Software::$version);
$twig->addGlobal("codename", Software::$codename);
$twig->addGlobal("software", Software::$name);

$criticalError = false;
$checks = array_fill_keys(array("configWritable", "mysql"), true);

if (PHP_VERSION_ID < 50500) {
  $checks["php"] = "<span class='label important'>".PHP_VERSION."</span>";
  $criticalError = true;
} else {
  $checks["php"] = "<span class='label success'>".PHP_VERSION."</span>";
}

// Check that we can use the config directory
if (file_exists("../config")) {
  // Directory exists, check if it's writable
  if (!is_writable("../config")) {
    $checks["configWritable"] = "the <tt>config</tt> directory needs to be writable. Installation cannot continue.";
    $criticalError = true;
  }
} else {
  // Doesn't exist: try and create it
  if (!@mkdir("../config")) {
    $checks["configWritable"] = "the <tt>config</tt> directory could not be created. Installation cannot continue.";
    $criticalError = true;
  }
  chmod("../config", 0777);
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

$twig->addGlobal("checks", $checks);
$twig->addGlobal("error", $criticalError);

echo $twig->render("setup.twig");
