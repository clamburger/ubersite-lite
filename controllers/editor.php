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
    $twig->addGlobal('title', 'Questionnaire Editor');
    $stmt = $dbh->query("SELECT * FROM questionnaires");

    $questionnaires = [];
    foreach ($stmt as $row) {
        $questionnaires[] = new Questionnaire($row);
    }
    $twig->addGlobal('questionnaires', $questionnaires);
} elseif ($id === 'new') {
    $query = <<<SQL
    INSERT INTO questionnaires (Name, Pages, Intro) VALUES (
      'Untitled Questionnaire', '{"Questions": {}, "Groups": {}, "Pages": {}, "PageOrder": []}', ''
    );
SQL;
    $dbh->exec($query);
    header('Location: /editor');
    exit;
}

// Check that the questionnaire exists, and if it does, load up information about it
/*$stmt = $dbh->prepare('SELECT * FROM questionnaires WHERE Id = ?');
$stmt->execute([$id]);
if (!$row = $stmt->fetch()) {
    header('Location: /choose?src=editor');
    exit;
}

$title = $row['Name'];
$twig->addGlobal('title', $title);
*/
