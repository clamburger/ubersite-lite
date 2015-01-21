<?php
namespace Ubersite\Questionnaire;

class Page
{
  public $pageID;
  public $title;
  public $intro = false;
  public $questions = [];
  
  public function __construct($details, $questions, $groups)
  {
    $this->pageID = $details->PageID;
    $this->title = $details->Title;
    foreach ($details->Questions as $ID) {
      if (isset($groups[$ID])) {
        $this->questions[] = $groups[$ID];
      } elseif (isset($questions[$ID])) {
        $this->questions[] = $questions[$ID];
      } else {
        throw new \Exception("Couldn't find group or question with ID $ID");
      }
    }
    if (isset($details->Intro)) {
      $this->intro = $details->Intro;
    }
  }

  public function renderHTML()
  {
    $out = "";
    if ($this->intro) {
      $out .= "<p>$this->intro</p>";
    }
    /** @var Group|Question $item */
    foreach ($this->questions as $item) {
      $out .= $item->renderHTML();
    }
    return $out;
  }
  
  public function __toString()
  {
    return $this->title;
  }
}
