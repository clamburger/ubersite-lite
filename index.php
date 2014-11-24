<?php
require 'vendor/autoload.php';
require 'includes/functions.php';

use Ubersite\bTemplate;
use Ubersite\DatabaseManager;
use Ubersite\MessageQueue;
use Ubersite\NullUser;
use Ubersite\Software;
use Ubersite\User;

if (!file_exists('config/config.php')) {
  header('Location: /setup/setup.php');
}

require 'config/config.php';

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

$twig->addGlobal('campname', CAMP_NAME);

$messages = new MessageQueue();

$user = new NullUser();
$script = explode("/", $_SERVER['SCRIPT_NAME']);
$pageName = $PAGE;

// Process user session and details
session_start();

// Populate array of users (UserID => Name)
$stmt = $dbh->query('SELECT * FROM users');
$people = array();
while ($row = $stmt->fetch()) {
  $people[$row['UserID']] = new User($row);
}

if (isset($_SESSION['username'])) {
  $username = $_SESSION['username'];
  // If the logged in user no longer exists, something bad happened.
  if (!isset($people[$username])) {
    header('Location: /logout');
  }
  $user = $people[$username];

} else {
  // Redirect to login page if not logged in
  if ($pageName != 'login') {
    if ($pageName == 'logout') {
      header('Location: /login');
    } else {
      header("Location: /login/$pageName");
    }
  }
}

// Disable error reporting for campers
if (!$user->isLeader()) {
  error_reporting(0);
}

if (isset($_GET['standalone'])) {
  $twig->addGlobal('standalone', true);
  $standalone = true;
  $twig->addGlobal('standalone-logo', dataURI("resources/img/logo.png", "image/png"));
  $twig->addGlobal('stand-alone-icon', dataURI("resources/img/icon.png", "image/png"));
  $layoutCSS = file_get_contents("resources/css/layout.css");
  $colourCSS = file_get_contents("resources/css/winter.css");
  $twig->addGlobal('standalone-style', $layoutCSS . "\n\n" . $colourCSS);
} else {
  $twig->addGlobal('standalone', false);
  $standalone = false;
}

if (!$user->isLeader()) {
  // Menu items with the "restricted" attribute will only be shown to leaders.
  $menu = array_filter($menu, function($menuItem) {
    return !isset($menuItem['restricted']);
  });
}

$loginURL = $user->LoggedIn ? '/login' : '';

// Construct the HTML for the navigation bar.
$menuHTML = "";
foreach ($menu as $filename => $menuItem) {
  $menuHTML .= "<li>";
  $menuHTML .= "\t<li><a href='{$loginURL}/{$filename}'>{$menuItem['name']}</a></li>\n";
  $menuHTML .= "</li>\n";
}

$twig->addGlobal('menu', $menuHTML);

$twig->addGlobal('head', false);

$twig->addGlobal('loginURL', $loginURL);

// TODO: remove this when possible
$twig->addGlobal('softwareFullName', Software::getFullName());

// New stuff! Part of the 2012 refactor
// TODO: we probably shouldn't be using $twig->addGlobal so much
$twig->addGlobal("user", $user);
$twig->addGlobal("messages", $messages->getAllMessageHTML());
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $twig->addGlobal("form", $_POST);
}
$twig->addGlobal("software", new Software());

// Include the specified page
if (file_exists("controllers/$PAGE.php")) {
  require_once("controllers/$PAGE.php");
} else {
  header('HTTP/1.0 404 Not Found');
  echo '404 Not Found';
}
