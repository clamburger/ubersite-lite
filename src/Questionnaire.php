<?php
namespace Ubersite;

use Ubersite\Questionnaire\Group;
use Ubersite\Questionnaire\Page;
use Ubersite\Questionnaire\Question;

class Questionnaire
{
    public $id;
    public $title;
    public $intro;

    public $pages;
    public $groups;
    public $questions;

    public function __construct($row)
    {
        $this->id = $row['Id'];
        $this->title = $row['Name'];
        $this->intro = $row['Intro'];

        $data = json_decode($row['Pages']);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception(
                'Failed to parse questionnaire JSON. The following error was given: ' . json_last_error_msg()
            );
        }

        foreach ($data->Questions as $questionID => $question) {
            $question->QuestionID = $questionID;
            $this->questions[$questionID] = new Question($question);
        }

        foreach ($data->Groups as $groupID => $group) {
            $group->GroupID = $groupID;
            $this->groups[$groupID] = new Group($group, $this->questions);
        }

        foreach ($data->Pages as $pageID => $page) {
            $page->PageID = $pageID;
            $this->pages[$pageID] = new Page($page, $this->questions, $this->groups);
        }
    }
}
