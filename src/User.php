<?php
namespace Ubersite;

/**
 * Holds information about a user.
 */
class User {
  public $Username;
  public $Name;
  public $Role;
  public $DutyTeam;

  public $LoggedIn = true;

  public function __construct($row) {
    $this->Username = $row['Username'];
    $this->Name = $row['Name'];
    $this->Role = $row['Role'];
    $this->DutyTeam = $row['DutyTeam'];
  }

  public function isLeader() {
    return in_array($this->Role, ['leader', 'director']);
  }
}
