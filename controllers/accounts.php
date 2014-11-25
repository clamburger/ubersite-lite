<?php
use Ubersite\Message;

$twig->addGlobal('submit', "Create User");

# Check how many people there are in the database right now
$userCount = count($people);

$selectNone = false;

if (isset($_POST['action']) && $user->isLeader()) {
  $twig->addGlobal('edit-ID', $_POST['userID']);
  $twig->addGlobal('edit-name', $_POST['name']);
  $twig->addGlobal('edit-duyteam', $_POST['dutyTeam']);

  # New account submission
  if ($_POST['action'] == "new") {
    $twig->addGlobal('edit-ID', $_POST['userIDinput']);
    $ID = $_POST['userIDinput'];
    if (isset($people[$ID])) {
      $messages->addMessage(new Message("error", "That ID already exists!"));
    } else {
      $name = trim($_POST['name']);
      if (empty($name)) {
        $messages->addMessage(new Message("error", "Name cannot be blank!"));
      } else {
        $password = password_hash($ID, PASSWORD_DEFAULT);

        $query = 'INSERT INTO users (UserID, Name, Password, Category, DutyTeam)
                  VALUES(?, ?, ?, ?, ?)';
        $stmt = $dbh->prepare($query);
        $stmt->execute([$ID, $name, $password, $_POST['category'], $_POST['dutyteam']]);
        $messages->addMessage(new Message('success', 'Account successfully created!'));

        refresh();
      }
    }

  # Edit account submission
  } else {
    $twig->addGlobal('editing', true);
    $twig->addGlobal('edit-disabled', 'disabled="disabled"');

    $ID = $_POST['userID'];
    $name = trim($_POST['name']);
    if (empty($name)) {
      $messages->addMessage(new Message("error", "Name cannot be blank!"));
    } else {
      $query = 'UPDATE users SET Name = ?, Category = ?, DutyTeam = ? WHERE UserID = ?';
      $stmt = $dbh->prepare($query);
      $stmt->execute($name, $_POST['category'], $_POST['dutyteam'], $ID);
      $messages->addMessage(new Message('success', 'Account successfully modified.'));
      refresh();
    }
  }

}

# Edit link clicked
if ($SEGMENTS[1] == "edit") {
  $twig->addGlobal('editing', true);
  $stmt = $dbh->prepare('SELECT * FROM users WHERE UserID = ?');
  $stmt->execute([$SEGMENTS[2]]);
  if (!$row = $stmt->fetch()) {
    header("Location: /accounts");
  }

  $twig->addGlobal('edit-disabled', 'disabled="disabled"');
  $twig->addGlobal('edit-ID', $row['UserID']);
  $twig->addGlobal('edit-dutyteam', $row['DutyTeam']);
  $twig->addGlobal('edit-name', $row['Name']);
  $twig->addGlobal('submit', "Modify User");
}

# Delete link clicked
if ($SEGMENTS[1] == "delete") {
  $userToDelete = $SEGMENTS[2];
  if (!isset($people[$userToDelete])) {
    header("Location: /accounts");
  } else {
    if ($SEGMENTS[3] == "confirm") {
      if (!isset($_SESSION['deleteID'])) {
        $messages->addMessage(new Message("error",
          "Cannot find original deletion request. You will need to press \"delete\" again."));
      } else if (time() - $_SESSION['deleteTime'] > 30) {
        $messages->addMessage(new Message("error",
          "You took too long to confirm. You will need to press \"delete\" again."));
      } else if ($_SESSION['deleteID'] != $userToDelete) {
        $messages->addMessage(new Message("error",
          "You have confirmed the wrong ID. You will need to press \"delete\" again."));
      } else {
        $stmt = $dbh->prepare('DELETE FROM users WHERE UserID = ?');
        $stmt->execute([$userToDelete]);
        $messages->addMessage(new Message("success",
          "You have successfully deleted {$people[$userToDelete]}'s account."));
      }
      unset($_SESSION['deleteID']);
      unset($_SESSION['deleteTime']);
    } else {
      if ($userToDelete == $user->UserID) {
          $messages->addMessage(new Message("error", "You cannot delete your own account!"));
      } else {
          $_SESSION['deleteID'] = $userToDelete;
          $_SESSION['deleteTime'] = time();
          $messages->addMessage(new Message("warning", "Are you absolutely positive that you want to delete {$people[$userToDelete]->Name}'s account?" .
              " | <a href='/accounts/delete/$userToDelete/confirm'>Confirm deletion</a>."));
      }
    }
  }
}

# Populate the "category" dropdown list
$categories = [];
foreach (['camper', 'leader', 'director', 'cook', 'visitor'] as $category) {
  $categories[$category] = false;
}
$twig->addGlobal('categories', $categories);

// This sorts the list of people by role.
usort($people, function($user1, $user2) {
  $precedence = array_flip(['director', 'leader', 'camper', 'cook', 'visitor']);

  // The max(1) in each of these will make directors be sorted the same as leaders.
  $c = max(1, $precedence[$user1->Category]);
  $d = max(1, $precedence[$user2->Category]);

  // If the category is the same, sort by the username.
  if ($c === $d) {
    return strcmp($user1->UserID, $user2->UserID);
  }

  return ($c < $d) ? -1 : 1;
});

$twig->addGlobal('users', $people);
