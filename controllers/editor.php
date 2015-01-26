<?php
use Ubersite\Message;
use Ubersite\Questionnaire;
use Ubersite\Questionnaire\Question;
use Ubersite\Utils;

if (!$user->isLeader()) {
    Utils::send403($twig);
}

$twig->addGlobal('title', 'Questionnaire Editor');

// Which questionnaire.
$id = $SEGMENTS[1];

if (!$id) {
    $twig->addGlobal('showAll', true);
    $stmt = $dbh->query("SELECT * FROM questionnaires");

    $questionnaires = [];
    foreach ($stmt as $row) {
        $questionnaires[] = new Questionnaire($row);
    }
    $twig->addGlobal('questionnaires', $questionnaires);

} else {
    $questionnaire = Questionnaire::loadFromDatabase($id);
    if ($questionnaire === null) {
        $messages->addMessage(new Message('error', 'Invalid questionnaire ID.'));
        header('Location: /editor');
        exit;
    }
    $twig->addGlobal('questionnaire', $questionnaire);

    $page = $SEGMENTS[2];
    if ($page !== null) {
        if (!isset($questionnaire->pages[$page-1])) {
            $messages->addMessage(new Message('error', 'Invalid page number.'));
            header('Location: /editor/' . $id);
            exit;
        }
        $twig->addGlobal('page', $questionnaire->pages[$page-1]);
        $twig->addGlobal('pageNumber', $page);
        $twig->addGlobal('answerTypes', Question::$answerTypes);
        $twig->addGlobal('title', $questionnaire->getTitle());
    }
}
