<?php
namespace Ubersite;

/**
 * A dummy user with minimal privileges.
 * Used for when nobody is logged in.
 */
class NullUser extends User {

  function __construct() {
    $this->Username = "";
    $this->Name = "";
    $this->Role = "camper";
    $this->LoggedIn = false;
  }

}
