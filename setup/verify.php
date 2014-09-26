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

$items = ["campName", "campYear", "mysqlHost", "mysqlUser", "mysqlPassword", "mysqlDatabase"];
checkMissing();
if (!checkSuccess()) {
    fail();
}

try {
    $dsn = sprintf('mysql:host=%s', $config['mysqlHost']);
    $db = @new PDO($dsn, $config['mysqlUser'], $config['mysqlPassword']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->query("CREATE DATABASE IF NOT EXISTS `{$config['mysqlDatabase']}`");
    $db->query("USE `{$config['mysqlDatabase']}`");
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

$stmt = $db->query("SHOW TABLES LIKE 'people'");
if ($stmt->fetch()) {
    $results["mysqlDatabase"] = "error";
    $details["mysqlDatabase"] = "Database already in use";
    fail();
}

$CONFIG_FILE = "../camp-data/config/config.json";

$db->exec(file_get_contents("database.sql"));

# Other default config options
$config["questionnaireCondition"] = '$day == "Fri"';

# MySQL username and password stored in separate file for security
file_put_contents("../camp-data/config/database.php",
    '<?php
$MYSQL_USER = "'.$config["mysqlUser"].'";
$MYSQL_PASSWORD = "'.$config["mysqlPassword"].'";
$MYSQL_HOST = "'.$config["mysqlHost"].'";
$MYSQL_DATABASE = "'.$config["mysqlDatabase"].'";
?>');

unset($config["mysqlUser"]);
unset($config["mysqlPassword"]);
unset($config["mysqlHost"]);
unset($config["mysqlDatabase"]);

file_put_contents("../camp-data/config/config.json", json_encode($config));
copy("menu.json", "../camp-data/config/menu.json");

echo json_encode(["results" => $results, "details" => $details, "success" => true]);