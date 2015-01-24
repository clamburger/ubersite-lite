<?php
use Ubersite\Questionnaire;
use Ubersite\Questionnaire\Question;
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
$questions = $questionnaire->questions;
$groups = $questionnaire->groups;
$pages = $questionnaire->pages;

$twig->addGlobal('title', $questionnaire->title);

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

// Generate the special table at the start
if (isset($details->FeedbackTable)) {
    $output .= "<table class='feedback'>\n";
    $output .= "<tr>\n";
    $output .= "  <th>Person</th>\n";
    foreach ($details->FeedbackTable as $questionID) {
        $question = $questions[$questionID];
        $output .= "  <th>{$question->questionShort}</th>\n";
    }
    $output .= "</tr>\n";
    foreach ($allResponders as $username => $name) {
        $output .= "<tr>\n";
        $output .= "  <td style='white-space: nowrap;'>$name</td>\n";

        foreach ($details->FeedbackTable as $questionID) {
            /** @var Question $question */
            $question = $questions[$questionID];
            if (isset($allResponses[$questionID][$username])) {
                $response = $allResponses[$questionID][$username]['Answer'];
                $stringResponse = $question->getAnswerString($response);
                $other = "";
                if (isset($allResponses[$questionID."-other"][$username]['Answer'])) {
                    $other = "<br><small>".$allResponses[$questionID."-other"][$username]['Answer']."</small>";
                }
                if ($stringResponse === Question::OTHER_RESPONSE) {
                    $stringResponse = "Other";
                }
                $output .= "<td>$stringResponse $other</td>\n";
            } else {
                $output .= "  <td>--</td>";
            }
        }
        $output .= "</tr>\n";
    }
    $output .= "</table>\n";
}

foreach ($pages as $page) {
    $output .= "<h2>{$page->title}</h2>\n";
    foreach ($page->questions as $question) {
        if (isset($details->FeedbackTable) && $question instanceof Question
         && in_array($question->id, $details->FeedbackTable, true)) {
            continue;
        }
        $output .= $question->renderFeedback($allResponses, $people);
    }
}

$twig->addGlobal("id", $id);
$twig->addGlobal("output", $output);
$twig->addGlobal("smallgroup", $smallgroup);
