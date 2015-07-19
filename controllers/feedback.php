<?php
use Ubersite\Questionnaire;
use Ubersite\Utils;

if (!$user->isLeader()) {
    Utils::send403($twig);
}

$id = $SEGMENTS[1];
$questionnaire = Questionnaire::loadFromDatabase($id);
if ($questionnaire === null) {
    header('Location: /choose?src=feedback');
    exit;
}
$pages = $questionnaire->pages;

$twig->addGlobal('title', $questionnaire->getTitle());

if ($SEGMENTS[2] == 'smallgroup' && strlen((string)$user->smallGroup) > 0) {
    $smallgroup = true;
    $where = "AND SmallGroup = ?";
} else {
    $smallgroup = false;
    $where = '';
}

// Find the responses
$query = "SELECT Name, Username, Responses FROM responses
          INNER JOIN `users` USING(Username) WHERE QuizId = ?
          $where ORDER BY Name ASC";
$stmt = $dbh->prepare($query);

if ($smallgroup) {
    $stmt->execute([$id, $user->smallGroup]);
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

foreach ($pages as $page) {
    foreach ($page->sections as $section) {
        $section->renderFeedback($allResponses, $people);
    }
}

$twig->addGlobal("questionnaire", $questionnaire);
$twig->addGlobal("smallgroup", $smallgroup);
