<?php
namespace Ubersite;

/**
 * Holds information about a user.
 */
class User {
  public $UserID;
  public $Name;
  public $Category;
  public $DutyTeam;

  public $LoggedIn = true;

  function __construct($row) {
    $this->UserID = $row['UserID'];
    $this->Name = $row['Name'];
    $this->Category = $row['Category'];
    $this->DutyTeam = $row['DutyTeam'];
  }

  function isCamper() {
    return $this->Category == "camper";
  }

  function isLeader() {
    return !$this->isCamper();
  }
}
