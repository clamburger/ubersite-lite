<?php
use Ubersite\Questionnaire;
use Ubersite\Utils;

if (!$user->isLeader()) {
    Utils::send403($twig);
}

$id = $SEGMENTS[1];

if (!$id) {
    header('Location: /choose?src=feedback');
    exit;
}

$id = intval($id);

$stmt = $dbh->prepare('SELECT * FROM questionnaires WHERE Id = ?');
$stmt->execute([$id]);
if (!$row = $stmt->fetch()) {
    header("Location: /choose?src=feedback");
    exit;
}
$questionnaire = new Questionnaire($row);
$pages = $questionnaire->pages;

$twig->addGlobal('title', $questionnaire->getTitle());

if ($SEGMENTS[2] == 'smallgroup') {
    $where = "AND DutyTeam = (SELECT DutyTeam FROM users WHERE Username = ?)";
} else {
    $where = '';
}

$smallgroup = $SEGMENTS[2] == 'smallgroup';

// Find the responses
$query = "SELECT Name, Username, Responses FROM responses
          INNER JOIN `users` USING(Username) WHERE Role = 'camper' $where
          AND QuizId = ? ORDER BY Name ASC";
$stmt = $dbh->prepare($query);

if ($smallgroup) {
    $stmt->execute([$id, $user->Username]);
} else {
    $stmt->execute([$id]);
}

$allResponses = [];
$allResponders = [];

while ($row = $stmt->fetch()) {
    $responses = json_decode($row['Responses']);
    $allResponders[$row['Username']] = $people[$row['Username']];
    foreach ($responses as $questionID => $answer) {
        if (!$answer) {
            continue;
        }
        if (!isset($allResponses[$questionID])) {
            $allResponses[$questionID] = [];
        }
        $allResponses[$questionID][$row['Username']] = ["Username" => $row['Username'], "Answer" => $answer];
    }
}

asort($allResponders);

$output = "";

foreach ($pages as $page) {
    $output .= "<h2>{$page->title}</h2>\n";
    foreach ($page->sections as $question) {
        $output .= $question->renderFeedback($allResponses, $people);
    }
}

$twig->addGlobal("id", $id);
$twig->addGlobal("output", $output);
$twig->addGlobal("smallgroup", $smallgroup);
