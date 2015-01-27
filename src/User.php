<?php
namespace Ubersite;

/**
 * Holds information about a user.
 */
class User
{
    public $username;
    public $name;
    public $role;
    public $smallGroup;

    public $loggedIn = true;

    public static $validRoles = ['camper', 'leader', 'director', 'cook', 'visitor'];

    public function __construct($row)
    {
        $this->username = $row['Username'];
        $this->name = $row['Name'];
        $this->role = $row['Role'];
        $this->smallGroup = $row['SmallGroup'];
    }

    public function isLeader()
    {
        return in_array($this->role, ['leader', 'director']);
    }
}
