<?php
namespace Ubersite\Questionnaire;

class Page implements \JsonSerializable
{
    /** @var string */
    public $title;
    public $intro;
    public $sections = [];
  
    public function __construct($details)
    {
        $this->title = $details->Title;
        foreach ($details->Sections as $section) {
            $this->sections[] = new Section($section);
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
        /** @var Question $item */
        foreach ($this->sections as $item) {
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
        foreach ($this->sections as $question) {
            $questions[$question->id] = $question;
        }
        $return['Questions'] = $questions;
        if ($this->intro !== null) {
            $return['Intro'] = $this->intro;
        }

        return $return;
    }
}
