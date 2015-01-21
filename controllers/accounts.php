<?php
use Ubersite\Message;
use Ubersite\Utils;

$twig->addGlobal('submit', "Create User");

$roles = ['camper', 'leader', 'director', 'cook', 'visitor'];

// This stores the values for the role dropdown list as well as which role is selected
$roleList = array_fill_keys($roles, false);

$editing = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user->isLeader()) {
    if ($_POST['action'] == "new") {
      // Creating a new user
        $username = $_POST['username'];
        $name = $_POST['name'];
        $role = $_POST['role'];
        if (in_array($role, $roles)) {
            $roleList[$role] = true;
        }
        if ($username === '') {
            $messages->addMessage(new Message("error", "A username must be provided."));
        } elseif (isset($people[$username])) {
            $messages->addMessage(new Message("error", "The provided username already exists."));
        } elseif ($name === '') {
            $messages->addMessage(new Message("error", "The name cannot be blank."));
        } elseif ($role !== '' && !in_array($role, $roles)) {
            $messages->addMessage(new Message("error", "Invalid role provided."));
        } else {
          // The default password is the same as the username.
            $password = password_hash($username, PASSWORD_DEFAULT);
            $query = 'INSERT INTO users (Username, Name, Password, Role, DutyTeam)
                VALUES(?, ?, ?, ?, ?)';
            $stmt = $dbh->prepare($query);
            $stmt->execute([$username, $name, $password, $role, $_POST['dutyTeam']]);
            $messages->addMessage(new Message('success', 'Account successfully created!'));

            Utils::refresh();
        }

    } else {
      // Editing an account
        $editing = true;
        $username = $_POST['username'];
        $name = $_POST['name'];
        $role = $_POST['role'];
        if (in_array($role, $roles)) {
            $roleList[$role] = true;
        }
        if (!isset($people[$username])) {
            $messages->addMessage(new Message("error", "That user no longer exists."));
            Utils::refresh();
        } elseif ($name === '') {
            $messages->addMessage(new Message("error", "The name cannot be blank."));
        } elseif ($role !== '' && !in_array($role, $roles)) {
            $messages->addMessage(new Message("error", "Invalid role provided."));
        } else {
            $query = 'UPDATE users SET Name = ?, Role = ?, DutyTeam = ? WHERE Username = ?';
            $stmt = $dbh->prepare($query);
            $stmt->execute([$name, $role, $_POST['dutyTeam'], $username]);
            $messages->addMessage(new Message('success', 'Account successfully modified.'));
            Utils::refresh();
        }
    }
}

// Edit link clicked
if ($SEGMENTS[1] == "edit") {
    $editing = true;
    if (!isset($people[$SEGMENTS[2]])) {
        header("Location: /accounts");
        exit;
    }
    $stmt = $dbh->prepare('SELECT * FROM users WHERE Username = ?');
    $stmt->execute([$SEGMENTS[2]]);
    $row = $stmt->fetch();

    $form = [
    'username' => $row['username'],
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
        exit;
    } else {
        if ($SEGMENTS[3] == "confirm") {
            if (!isset($_SESSION['deleteID'])) {
                $messages->addMessage(
                    new Message(
                        "error",
                        "Cannot find original deletion request. You will need to press \"delete\" again."
                    )
                );
            } elseif (time() - $_SESSION['deleteTime'] > 30) {
                $messages->addMessage(
                    new Message("error", "You took too long to confirm. You will need to press \"delete\" again.")
                );
            } elseif ($_SESSION['deleteID'] != $username) {
                $messages->addMessage(
                    new Message("error", "You have confirmed the wrong ID. You will need to press \"delete\" again.")
                );
            } else {
                $stmt = $dbh->prepare('DELETE FROM users WHERE Username = ?');
                $stmt->execute([$username]);
                $messages->addMessage(
                    new Message("success", "You have successfully deleted {$people[$username]->Name}'s account.")
                );
                unset($people[$username]);
            }
            unset($_SESSION['deleteID']);
            unset($_SESSION['deleteTime']);
        } else {
            if ($username == $user->Username) {
                $messages->addMessage(new Message("error", "You cannot delete your own account!"));
            } else {
                $_SESSION['deleteID'] = $username;
                $_SESSION['deleteTime'] = time();
                $message = "Are you absolutely positive that you want to delete {$people[$username]->Name}'s account?" .
                " | <a href='/accounts/delete/$username/confirm'>Confirm deletion</a>.";
                $messages->addMessage(new Message("warning", $message));
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
        return strcmp($user1->Username, $user2->Username);
    }

    return ($c < $d) ? -1 : 1;
});

$twig->addGlobal('users', $people);
