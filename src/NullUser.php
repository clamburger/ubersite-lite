<?php
namespace Ubersite;

/**
 * A dummy user with minimal privileges.
 * Used for when nobody is logged in.
 */
class NullUser extends User
{

    public function __construct()
    {
        $this->username = "";
        $this->name = "";
        $this->role = "camper";
        $this->loggedIn = false;
    }
}
