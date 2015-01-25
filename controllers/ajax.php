<?php
use Ubersite\Questionnaire;
use Ubersite\Utils;

if (!$user->isLeader()) {
    Utils::send403($twig);
}

$action = $_POST['action'];

if ($action === 'create-questionnaire') {
    $query = <<<SQL
    INSERT INTO questionnaires (Name, Pages, Intro) VALUES (
      'Untitled Questionnaire', '{"Questions": {}, "Groups": {}, "Pages": []}', ''
    );
SQL;
    $dbh->exec($query);

} elseif ($action === 'duplicate-questionnaire') {
    $query = <<<SQL
      INSERT INTO questionnaires (Name, Pages, Intro)
      SELECT Name || ' (copy)', Pages, Intro FROM questionnaires WHERE Id = ?
SQL;
    $stmt = $dbh->prepare($query);
    $stmt->execute([$_POST['id']]);

} elseif ($action === 'delete-questionnaire') {
    $questionnaire = Questionnaire::loadFromDatabase($_POST['id']);
    $questionnaire->deleteQuestionnaire();

} elseif ($action === 'update-title') {
    $questionnaire = Questionnaire::loadFromDatabase($_POST['id']);
    $questionnaire->setTitle($_POST['text']);

} elseif ($action === 'update-intro-text') {
    $questionnaire = Questionnaire::loadFromDatabase($_POST['id']);
    $questionnaire->setIntro($_POST['text']);

} elseif ($action === 'duplicate-page') {
    $questionnaire = Questionnaire::loadFromDatabase($_POST['id']);
    $questionnaire->duplicatePage($_POST['page']);

} elseif ($action === 'delete-page') {
    $questionnaire = Questionnaire::loadFromDatabase($_POST['id']);
    $questionnaire->deletePage($_POST['page']);

} elseif ($action === 'move-page') {
    $questionnaire = Questionnaire::loadFromDatabase($_POST['id']);
    $questionnaire->movePage($_POST['page'], (int)$_POST['movement']);

} elseif ($action === 'create-page') {
    $questionnaire = Questionnaire::loadFromDatabase($_POST['id']);
    $questionnaire->createNewPage();

} elseif ($action === 'update-page-title') {
    $questionnaire = Questionnaire::loadFromDatabase($_POST['id']);
    $questionnaire->getPage($_POST['page'])->title = $_POST['text'];
    $questionnaire->updateDatabase();

} elseif ($action === 'update-page-intro') {
    $questionnaire = Questionnaire::loadFromDatabase($_POST['id']);
    $questionnaire->getPage($_POST['page'])->intro = $_POST['text'];
    $questionnaire->updateDatabase();
}
