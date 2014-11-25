<?php
namespace Ubersite;

/**
 * Holds information about a user.
 */
class User {
  public $UserID;
  public $Name;
  public $Role;
  public $DutyTeam;

  public $LoggedIn = true;

  public function __construct($row) {
    $this->UserID = $row['UserID'];
    $this->Name = $row['Name'];
    $this->Role = $row['Role'];
    $this->DutyTeam = $row['DutyTeam'];
  }

  public function isLeader() {
    return in_array($this->Role, ['leader', 'director']);
  }
}
