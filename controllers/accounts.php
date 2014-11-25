<?php
use Ubersite\Message;

$twig->addGlobal('submit', "Create User");

$roles = ['camper', 'leader', 'director', 'cook', 'visitor'];

// This stores the values for the role dropdown list as well as which role is selected
$roleList = array_fill_keys($roles, false);

$editing = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user->isLeader()) {

  if ($_POST['action'] == "new") {
    // Creating a new user
    $username = $_POST['userID'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    if (in_array($role, $roles)) {
      $roleList[$role] = true;
    }
    if ($username === '') {
      $messages->addMessage(new Message("error", "A username must be provided."));
    } else if (isset($people[$username])) {
      $messages->addMessage(new Message("error", "The provided username already exists."));
    } else if ($name === '') {
      $messages->addMessage(new Message("error", "The name cannot be blank."));
    } else if ($role !== '' && !in_array($role, $roles)) {
      $messages->addMessage(new Message("error", "Invalid role provided."));
    } else {
      // The default password is the same as the username.
      $password = password_hash($username, PASSWORD_DEFAULT);
      $query = 'INSERT INTO users (UserID, Name, Password, Role, DutyTeam)
                VALUES(?, ?, ?, ?, ?)';
      $stmt = $dbh->prepare($query);
      $stmt->execute([$username, $name, $password, $role, $_POST['dutyTeam']]);
      $messages->addMessage(new Message('success', 'Account successfully created!'));

      refresh();
    }

  } else {
    // Editing an account
    $editing = true;
    $username = $_POST['userID'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    if (in_array($role, $roles)) {
      $roleList[$role] = true;
    }
    if (!isset($people[$username])) {
      $messages->addMessage(new Message("error", "That user no longer exists."));
      refresh();
    } else if ($name === '') {
      $messages->addMessage(new Message("error", "The name cannot be blank."));
    } else if ($role !== '' && !in_array($role, $roles)) {
      $messages->addMessage(new Message("error", "Invalid role provided."));
    } else {
      $query = 'UPDATE users SET Name = ?, Role = ?, DutyTeam = ? WHERE UserID = ?';
      $stmt = $dbh->prepare($query);
      $stmt->execute([$name, $role, $_POST['dutyTeam'], $username]);
      $messages->addMessage(new Message('success', 'Account successfully modified.'));
      refresh();
    }
  }
}

// Edit link clicked
if ($SEGMENTS[1] == "edit") {
  $editing = true;
  if (!isset($people[$SEGMENTS[2]])) {
    header("Location: /accounts");
  }
  $stmt = $dbh->prepare('SELECT * FROM users WHERE UserID = ?');
  $stmt->execute([$SEGMENTS[2]]);
  $row = $stmt->fetch();

  $form = [
    'userID' => $row['UserID'],
    'name' => $row['Name'],
    'role' => $row['Role'],
    'dutyTeam' => $row['DutyTeam']
  ];
  $roleList[$row['Role']] = true;
  $twig->addGlobal('form', $form);
}

$twig->addGlobal('editing', $editing);
$twig->addGlobal('roles', $roleList);

# Delete link clicked
if ($SEGMENTS[1] == "delete") {
  $username = $SEGMENTS[2];
  if (!isset($people[$username])) {
    header("Location: /accounts");
  } else {
    if ($SEGMENTS[3] == "confirm") {
      if (!isset($_SESSION['deleteID'])) {
        $messages->addMessage(new Message("error",
          "Cannot find original deletion request. You will need to press \"delete\" again."));
      } else if (time() - $_SESSION['deleteTime'] > 30) {
        $messages->addMessage(new Message("error",
          "You took too long to confirm. You will need to press \"delete\" again."));
      } else if ($_SESSION['deleteID'] != $username) {
        $messages->addMessage(new Message("error",
          "You have confirmed the wrong ID. You will need to press \"delete\" again."));
      } else {
        $stmt = $dbh->prepare('DELETE FROM users WHERE UserID = ?');
        $stmt->execute([$username]);
        $messages->addMessage(new Message("success",
          "You have successfully deleted {$people[$username]->Name}'s account."));
        unset($people[$username]);
      }
      unset($_SESSION['deleteID']);
      unset($_SESSION['deleteTime']);
    } else {
      if ($username == $user->UserID) {
          $messages->addMessage(new Message("error", "You cannot delete your own account!"));
      } else {
          $_SESSION['deleteID'] = $username;
          $_SESSION['deleteTime'] = time();
          $messages->addMessage(new Message("warning", "Are you absolutely positive that you want to delete {$people[$username]->Name}'s account?" .
              " | <a href='/accounts/delete/$username/confirm'>Confirm deletion</a>."));
      }
    }
  }
}

// This sorts the list of people by role.
usort($people, function($user1, $user2) {
  $precedence = array_flip(['director', 'leader', 'camper', 'cook', 'visitor']);

  // The max(1) in each of these will make directors be sorted the same as leaders.
  $c = max(1, $precedence[$user1->Role]);
  $d = max(1, $precedence[$user2->Role]);

  // If the role is the same, sort by the username.
  if ($c === $d) {
    return strcmp($user1->UserID, $user2->UserID);
  }

  return ($c < $d) ? -1 : 1;
});

$twig->addGlobal('users', $people);
