<?php
require 'vendor/autoload.php';

use Ubersite\Config;
use Ubersite\DatabaseManager;
use Ubersite\MessageBank;
use Ubersite\NullUser;
use Ubersite\Software;
use Ubersite\User;
use Ubersite\Utils;

$config = new Config();

if (!$config->isLoaded()) {
    header('Location: /setup');
    exit;
}

ini_set('display_errors', 'On');
$dbh = DatabaseManager::get();

// URL rewriter
// Courtesy of http://stackoverflow.com/questions/893218/rewrite-for-all-urls
$_SERVER['REQUEST_URI_PATH'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$SEGMENTS = explode('/', trim($_SERVER['REQUEST_URI_PATH'], '/'));
$SEGMENTS = array_map("strtolower", $SEGMENTS);

for ($i = 0; $i <= 9; $i++) {
    if (!isset($SEGMENTS[$i])) {
        $SEGMENTS[$i] = null;
    }
}

$PAGE = $SEGMENTS[0];
// End URL rewriter

if (strlen($PAGE) == 0) {
    $PAGE = 'questionnaire';
}

header("Content-Type: text/html; charset=utf-8");

// Register the Twig autoloader so we can use Twig templates
$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader);

// Process user session and details
session_start();

$messages = new MessageBank();

$user = new NullUser();
$script = explode("/", $_SERVER['SCRIPT_NAME']);
$pageName = $PAGE;

// Special handling for logout page
if ($pageName == 'logout') {
    session_destroy();
    header('Location: /');
    exit;
}

// Populate array of users (Username => User object)
$stmt = $dbh->query('SELECT * FROM users');
$people = [];
while ($row = $stmt->fetch()) {
    $people[$row['Username']] = new User($row);
}

if (count($people) === 0) {
    if ($PAGE != 'account-import') {
        header('Location: /account-import');
        exit;
    }
} else {
    if (isset($_SESSION['username'])) {
      // If the logged in user no longer exists, something bad happened.
        if (!isset($people[$_SESSION['username']])) {
            header('Location: /logout');
            exit;
        }
        $user = $people[$_SESSION['username']];

    } else {
      // Redirect to login page if not logged in
        if ($pageName != 'login') {
            header("Location: /login/$pageName");
            exit;
        }
    }
}

// Disable error reporting for campers
if (!$user->isLeader()) {
    error_reporting(0);
}

// Standalone mode includes all relevant resources directly onto the page so that only the HTML
// file needs to be saved to get a complete copy.
if (isset($_GET['standalone'])) {
    $layoutCSS = file_get_contents("resources/css/layout.css");
    $colourCSS = file_get_contents("resources/css/winter.css");

    $standalone = [
    'logo' => Utils::dataURI("resources/img/logo.png", "image/png"),
    'icon' => Utils::dataURI("resources/img/icon.png", "image/png"),
    'css' => $layoutCSS . "\n\n" . $colourCSS
    ];
    $twig->addGlobal('standalone', $standalone);
}

$loginURL = $user->LoggedIn ? '/login' : '';

// TODO: we probably shouldn't be using $twig->addGlobal so much

$twig->addGlobal('config', $config);
$twig->addGlobal('loginURL', $loginURL);
$twig->addGlobal("software", new Software());
$twig->addGlobal("user", $user);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $twig->addGlobal("form", $_POST);
}

// Include the specified page
if (file_exists("controllers/$PAGE.php")) {
    require_once("controllers/$PAGE.php");
    $twig->addGlobal('messagebank', $messages);
    echo $twig->render("$PAGE.twig");
} else {
    header('HTTP/1.0 404 Not Found');
    echo '404 Not Found';
}
