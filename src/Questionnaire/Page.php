<?php
namespace Ubersite\Questionnaire;

class Page implements \JsonSerializable
{
    /** @var string */
    public $title;
    public $intro;
    public $questions = [];
  
    public function __construct($details)
    {
        $this->title = $details->Title;
        foreach ($details->Questions as $id => $question) {
            if (isset($question->Title)) {
                $this->questions[] = new Group($id, $question);
            } else {
                $this->questions[] = new Question($id, $question);
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
            $questions[$question->id] = $question;
        }
        $return['Questions'] = $questions;
        if ($this->intro !== null) {
            $return['Intro'] = $this->intro;
        }

        return $return;
    }
}
