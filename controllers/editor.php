<?php
use Ubersite\Message;
use Ubersite\Questionnaire;
use Ubersite\Utils;

if (!$user->isLeader()) {
    Utils::send403($twig);
}

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
    $page = $SEGMENTS[2];
    $stmt = $dbh->prepare('SELECT * FROM questionnaires WHERE Id = ?');
    $stmt->execute([$id]);
    if (!$row = $stmt->fetch()) {
        $messages->addMessage(new Message('error', 'Invalid questionnaire ID.'));
        header('Location: /editor');
        exit;
    }
    $questionnaire = new Questionnaire($row);
    $twig->addGlobal('questionnaire', $questionnaire);
}
