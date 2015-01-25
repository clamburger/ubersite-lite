<?php
use Ubersite\Message;
use Ubersite\Questionnaire;
use Ubersite\Utils;

if (!$user->isLeader()) {
    Utils::send403($twig);
}

$action = $_POST['action'];

if ($action === 'create-questionnaire') {
    $query = "INSERT INTO questionnaires (Name, Pages, Intro) VALUES ('Untitled Questionnaire', '[]', '')";
    $dbh->exec($query);
    exit;
}

$questionnaire = Questionnaire::loadFromDatabase($_POST['id']);
if ($questionnaire === null) {
    $messages->addMessage(new Message('error', "Couldn't load questionnaire with ID {$_POST['id']}."));
    exit;
}

if ($action === 'duplicate-questionnaire') {
    $query = <<<SQL
      INSERT INTO questionnaires (Name, Pages, Intro)
      SELECT Name || ' (copy)', Pages, Intro FROM questionnaires WHERE Id = ?
SQL;
    $stmt = $dbh->prepare($query);
    $stmt->execute([$_POST['id']]);

} elseif ($action === 'delete-questionnaire') {
    $questionnaire->deleteQuestionnaire();

} elseif ($action === 'update-title') {
    $questionnaire->setTitle($_POST['text']);

} elseif ($action === 'update-intro-text') {
    $questionnaire->setIntro($_POST['text']);

} elseif ($action === 'duplicate-page') {
    $questionnaire->duplicatePage($_POST['page']);

} elseif ($action === 'delete-page') {
    $questionnaire->deletePage($_POST['page']);

} elseif ($action === 'move-page') {
    $questionnaire->movePage($_POST['page'], (int)$_POST['movement']);

} elseif ($action === 'create-page') {
    $questionnaire->createNewPage();

} elseif ($action === 'update-page-title') {
    $questionnaire->getPage($_POST['page'])->title = $_POST['text'];
    $questionnaire->updateDatabase();

} elseif ($action === 'update-page-intro') {
    $questionnaire->getPage($_POST['page'])->intro = $_POST['text'];
    $questionnaire->updateDatabase();

}
