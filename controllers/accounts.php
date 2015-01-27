<?php
use Ubersite\Message;
use Ubersite\Utils;
use Ubersite\User;

if (!$user->isLeader()) {
    Utils::send403($twig);
}

$twig->addGlobal('submit', "Create User");

$twig->addGlobal('roles', User::$validRoles);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Creating a new user
    $username = $_POST['username'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    if ($username === '') {
        $messages->addMessage(new Message("error", "A username must be provided."));
    } elseif (isset($people[$username])) {
        $messages->addMessage(new Message("error", "The provided username already exists."));
    } elseif ($name === '') {
        $messages->addMessage(new Message("error", "The name cannot be blank."));
    } elseif ($role !== '' && !in_array($role, User::$validRoles)) {
        $messages->addMessage(new Message("error", "Invalid role provided."));
    } else {
      // The default password is the same as the username.
        $password = password_hash($username, PASSWORD_DEFAULT);
        $query = 'INSERT INTO users (Username, Name, Password, Role, SmallGroup)
            VALUES(?, ?, ?, ?, ?)';
        $stmt = $dbh->prepare($query);
        $stmt->execute([$username, $name, $password, $role, $_POST['dutyTeam']]);
        $messages->addMessage(new Message('success', 'Account successfully created!'));

        Utils::refresh();
    }
}

// This sorts the list of people by username.
usort($people, function($user1, $user2) {
    return strcmp($user1->username, $user2->username);
});

$twig->addGlobal('users', $people);
