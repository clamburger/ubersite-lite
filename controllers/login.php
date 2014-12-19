<?php
use Ubersite\DatabaseManager;
use Ubersite\Message;

$redirect = $SEGMENTS[1];

# If the user is already logged in, redirect them to the index.
if (isset($_SESSION['username'])) {
  header("Location: /$redirect");
  exit;
}

$twig->addGlobal('form-username', false);

if (count($people) === 0) {
  $messages->addMessage(new Message('warning', 'Warning: There are no users in the database.'));
}

if (isset($_POST['username']) && isset($_POST['password'])) {
  # Look up the user and validate them.
  $username = strtolower($_POST['username']);
  $password = $_POST['password'];

  $dbh = DatabaseManager::get();
  $stmt = $dbh->prepare('SELECT Password FROM users WHERE Username = ?');
  $stmt->execute([$username]);

  if (!$row = $stmt->fetch()) {
    $messages->addMessage(new Message('error', "That user doesn't exist."));
  } else if ((is_null($row['Password']) && $password === $username) || password_verify($password, $row['Password'])) {
    $_SESSION['username'] = $username;
    header("Location: $redirect");
    exit;
  } else {
    var_dump($row);
    $messages->addMessage(new Message("error", "The specified password was incorrect."));
    $twig->addGlobal("form-username", $_POST['username']);
  }
}
