<?php
function valid($var) {
  global $config;

	if (!isset($_POST[$var])) {
		return false;
	}
	
	$value = trim($_POST[$var]);
	if (empty($value)) {
		return false;
	}

    $config[$var] = $value;
	return true;
}

function checkMissing() {
	global $results, $details, $items, $v;

	foreach ($items as $item) {
		if (valid($item)) {
			$results[$item] = "";
			$details[$item] = "";
			$v[$item] = true;
		} else {
			$results[$item] = "error";
			$details[$item] = "This field is required.";
			$v[$item] = false;
		}
	}

}

function checkSuccess() {
	global $results;

	if (count(array_keys($results, "")) + count(array_keys($results, "warning")) == count($results)) {
		$results = array_fill_keys(array_keys($results), "success");
		return true;
	} else {
		return false;
	}
}

function fail()
{
    global $results, $details;
    echo json_encode(["results" => $results, "details" => $details, "success" => false]);
    exit;
}

$results = $details = $config = $v = [];

$items = ["campName", "mysqlHost", "mysqlUser", "mysqlPassword", "mysqlDatabase"];
checkMissing();
if (!checkSuccess()) {
    fail();
}

try {
    $dsn = sprintf('mysql:host=%s', $config['mysqlHost']);
    $dbh = @new PDO($dsn, $config['mysqlUser'], $config['mysqlPassword']);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->query("CREATE DATABASE IF NOT EXISTS `{$config['mysqlDatabase']}`");
    $dbh->query("USE `{$config['mysqlDatabase']}`");
} catch (PDOException $e) {
    $error = $e->getCode();

    if ($error == 1045) {
        $results["mysqlUser"] = "error";
        $results["mysqlPassword"] = "error";
        $details["mysqlUser"] = "Username or password is invalid.";
    } else if ($error == 2002 || $error == 2003) {
        $results["mysqlHost"] = "error";
        $details["mysqlHost"] = "Cannot connect to server.";
    } else if ($error == 42000) {
        $results["mysqlUser"] = "error";
        $details["mysqlUser"] = "User has insufficient access";
    } else {
        $details["mysqlHost"] = "MySQL error $error (".$e->getMessage().")";
    }
    fail();
}

$stmt = $dbh->query("SHOW TABLES LIKE 'people'");
if ($stmt->fetch()) {
    $results["mysqlDatabase"] = "error";
    $details["mysqlDatabase"] = "Database already in use";
    fail();
}

$CONFIG_FILE = "../config/config/config.php";

$dbh->exec(file_get_contents("database.sql"));

file_put_contents("../config/config/config.php", json_encode($config));

echo json_encode(["results" => $results, "details" => $details, "success" => true]);
