<?php
namespace Ubersite;

/**
 * Holds information about a user.
 */
class User
{
    public $username;
    public $name;
    public $role = 'camper';
    public $smallGroup;

    public static $validRoles = ['camper', 'leader', 'director', 'cook', 'visitor'];

    public static function createFromRow($row)
    {
        $user = new User();
        $user->username = $row['Username'];
        $user->name = $row['Name'];
        $user->role = $row['Role'];
        $user->smallGroup = $row['SmallGroup'];
        return $user;
    }

    public function isLeader()
    {
        return in_array($this->role, ['leader', 'director']);
    }
}
