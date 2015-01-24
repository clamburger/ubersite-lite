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

} elseif ($id === 'new') {
    $query = <<<SQL
    INSERT INTO questionnaires (Name, Pages, Intro) VALUES (
      'Untitled Questionnaire', '{"Questions": {}, "Groups": {}, "Pages": []}', ''
    );
SQL;
    $dbh->exec($query);
    $messages->addMessage(new Message('success', 'New questionnaire successfully created.'));
    header('Location: /editor');
    exit;

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

    if ($page === 'new') {
        $questionnaire->createNewPage();
        header('Location: /editor/' . $id);
        exit;
    }
}

// Check that the questionnaire exists, and if it does, load up information about it
/*
*/
