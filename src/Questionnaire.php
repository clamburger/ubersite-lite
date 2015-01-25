<?php
namespace Ubersite;

use Ubersite\Questionnaire\Page;

class Questionnaire
{
    public $id;
    private $title;
    private $intro;

    /** @var Page[] */
    public $pages;

    public function __construct($row)
    {
        $this->id = $row['Id'];
        $this->title = $row['Name'];
        $this->intro = $row['Intro'];

        $pages = json_decode($row['Pages']);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception(
                'Failed to parse questionnaire JSON. The following error was given: ' . json_last_error_msg()
            );
        }

        foreach ($pages as $page) {
            $this->pages[] = new Page($page);
        }
    }

    public function deleteQuestionnaire()
    {
        $dbh = DatabaseManager::get();
        $stmt = $dbh->prepare('DELETE FROM questionnaires WHERE Id = ?');
        $stmt->execute([$_POST['id']]);
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        $this->updateDatabase();
    }

    public function getIntro()
    {
        return $this->intro;
    }

    public function setIntro($intro)
    {
        $this->intro = $intro;
        $this->updateDatabase();
    }

    public function createNewPage()
    {
        $this->pages[] = Page::createNew();
        $this->updateDatabase();
    }

    public function movePage($pageNumber, $movement)
    {
        $index = $pageNumber - 1;
        $newIndex = $index + $movement;

        if ($newIndex < 0 || $newIndex > count($this->pages) - 1) {
            return;
        }

        $page = array_splice($this->pages, $index, 1);
        array_splice($this->pages, $newIndex, 0, $page);
        $this->updateDatabase();
    }

    public function deletePage($pageNumber)
    {
        $index = $pageNumber - 1;
        array_splice($this->pages, $index, 1);
        $this->updateDatabase();
    }

    public function duplicatePage($pageNumber)
    {
        $newPage = clone $this->pages[$pageNumber-1];
        $newPage->title = $newPage->title . ' (copy)';
        $this->pages[] = $newPage;
        $this->updateDatabase();
    }

    public function getPage($pageNumber)
    {
        return $this->pages[$pageNumber - 1];
    }

    /**
     * Updates the database with changes made to the questionnaire.
     */
    public function updateDatabase()
    {
        $dbh = DatabaseManager::get();
        $stmt = $dbh->prepare('UPDATE questionnaires SET Name = ?, Pages = ?, Intro = ? WHERE Id = ?');
        $stmt->execute([$this->title, json_encode($this->pages, JSON_PRETTY_PRINT), $this->intro, $this->id]);
    }

    public static function loadFromDatabase($id)
    {
        $dbh = DatabaseManager::get();
        $stmt = $dbh->prepare('SELECT * FROM questionnaires WHERE Id = ?');
        $stmt->execute([$id]);

        if (!$row = $stmt->fetch()) {
            return null;
        }

        return new Questionnaire($row);
    }
}
