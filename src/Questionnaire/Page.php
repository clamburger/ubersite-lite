<?php
namespace Ubersite\Questionnaire;

class Page implements \JsonSerializable
{
    /** @var string */
    public $title = 'Untitled Page';
    public $intro;
    /** @var Section[] */
    public $sections = [];
  
    public function populateFromDetails($details)
    {
        $this->title = $details['Title'];
        foreach ($details['Sections'] as $sectionDetails) {
            $section = new Section();
            $section->populateFromDetails($sectionDetails);
            $this->sections[] = $section;
        }
        if (isset($details['Intro'])) {
            $this->intro = $details['Intro'];
        }
    }

    public function getQuestionCount()
    {
        $count = 0;
        foreach ($this->sections as $section) {
            $count += count($section->questions);
        }
        return $count;
    }

    public function addSection()
    {
        $this->sections[] = new Section();
    }

    public function duplicateSection($sectionNumber)
    {
        $section = clone $this->sections[$sectionNumber];
        array_splice($this->sections, $sectionNumber, 0, [$section]);
    }

    public function deleteSection($section)
    {
        array_splice($this->sections, $section, 1);
    }

    public function getSection($section)
    {
        return $this->sections[$section];
    }

    public function moveSection($index, $movement)
    {
        $newIndex = $index + $movement;

        if ($newIndex < 0 || $newIndex > count($this->sections) - 1) {
            return;
        }

        $section = array_splice($this->sections, $index, 1);
        array_splice($this->sections, $newIndex, 0, $section);
    }

    public function __toString()
    {
        return $this->title;
    }

    public function jsonSerialize()
    {
        $return = ['Title' => $this->title, 'Sections' => $this->sections];
        if ($this->intro !== null) {
            $return['Intro'] = $this->intro;
        }

        return $return;
    }
}
