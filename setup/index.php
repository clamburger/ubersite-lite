<?php
require '../vendor/autoload.php';

use Ubersite\Config;
use Ubersite\Software;

error_reporting(E_ALL);

$config = new Config();
if ($config->isLoaded()) {
    header("Location: ../");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // If the database already exists, just use it as is.
    $createDatabase = !file_exists('../config/database.db');
    $db = new SQLite3('../config/database.db');
    if ($createDatabase) {
      // We need to do each query individually as the driver doesn't support multiple queries.
      // Make sure there aren't any other semicolons other than the ones at the end of statements
      // or things will go horribly wrong.
        $schemaCommands = explode(';', file_get_contents('database.sql'));
        foreach ($schemaCommands as $sql) {
            $db->exec($sql);
        }
    }

    $config = file_get_contents('example.config.php');
    $config = str_replace('[[CAMP_NAME]]', $_POST['campName'], $config);
    file_put_contents('../config/config.php', $config);

    header("Location: ../");
    exit;
}

$loader = new Twig_Loader_Filesystem('.');
$twig = new Twig_Environment($loader);

$twig->addGlobal('software', new Software());

$checks = [];
if (PHP_VERSION_ID < 50500) {
    $checks['php'] = [PHP_VERSION, 'important', 'PHP needs to be upgraded'];
} else {
    $checks['php'] = [PHP_VERSION, 'success'];
}

// Check that we can use the config directory
if (file_exists('../config')) {
  // Directory exists, check if it's writable
    if (!is_writable('../config')) {
        $checks['config'] = ['no', 'important', 'the <code>config</code> directory needs to be writable.'];
    }
} else {
  // Doesn't exist: try and create it
    if (!@mkdir('../config')) {
        $checks['config'] = ['no', 'important', "the <code>config</code> directory couldn't be created."];
    }
    chmod('../config', 0777);
}

if (!isset($checks['config'])) {
    $checks['config'] = ['yes', 'success'];
}

if (!in_array('sqlite', PDO::getAvailableDrivers()) || !class_exists('SQLite3')) {
    $checks['sqlite'] = ['no', 'error', 'you may need to install <code>php5-sqlite3</code>.'];
} elseif (file_exists('../config/database.db')) {
    $checks['sqlite'] = ['warning', 'warning', 'the database already exists. It will not be overwritten.'];
} else {
    $checks['sqlite'] = ['yes', 'success'];
}

$error = false;
foreach ($checks as $key => $data) {
    $html = "<span class='alert alert-{$data[1]}'>{$data[0]}</span>";
    if (isset($data[2])) {
        $html .= " - {$data[2]}";
    }
    if ($data[1] == 'important') {
        $error = true;
    }
    $checks[$key] = $html;
}

$twig->addGlobal('checks', $checks);
$twig->addGlobal('error', $error);

echo $twig->render('setup.twig');
