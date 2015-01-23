<?php
namespace Ubersite;

class Questionnaire
{
    public $id;
    public $title;

    public $pages;
    public $groups;
    public $questions;

    public function __construct($row)
    {
        $this->id = $row['Id'];
        $this->title = $row['Name'];

        $data = json_decode($row['Pages'], true);
        $this->pages = $data['Pages'];
        $this->groups = $data['Groups'];
        $this->questions = $data['Questions'];
    }
}
