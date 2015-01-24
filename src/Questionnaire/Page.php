<?php
namespace Ubersite\Questionnaire;

class Page implements \JsonSerializable
{
    /** @var string */
    public $title;
    public $intro = null;
    public $questions = [];
  
    public function __construct($details, $questions, $groups)
    {
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

    public static function createNew()
    {
        $details = new \stdClass();
        $details->Title = 'Untitled Page';
        $details->Questions = [];
        return new Page($details, [], []);
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

    public function jsonSerialize()
    {
        $return = ['Title' => $this->title];

        $questions = [];
        foreach ($this->questions as $question) {
            $questions[] = $question->id;
        }
        $return['Questions'] = $questions;
        if ($this->intro !== null) {
            $return['Intro'] = $this->intro;
        }

        return $return;
    }
}
