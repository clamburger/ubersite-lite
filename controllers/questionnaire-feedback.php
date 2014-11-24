<?php
use Ubersite\Questionnaire\Group;
use Ubersite\Questionnaire\Question;
use Ubersite\Questionnaire\Page;

$id = $SEGMENTS[1];

if (!$id) {
  header('Location: /questionnaire-choose?src=questionnaire-feedback');
  exit;
}

$id = intval($id);

$stmt = $dbh->prepare('SELECT * FROM questionnaires WHERE Id = ?');
$stmt->execute([$id]);
if (!$row = $stmt->fetch()) {
  header("Location: /questionnaire-choose?src=questionnaire-feedback");
  exit;
}

$title = $row["Name"];
$twig->addGlobal('title', $title);
$details = json_decode($row["Pages"]);

$questions = [];
$groups = [];
$pages = [];

foreach ($details->Questions as $questionID => $question) {
  $question->QuestionID = $questionID;
  $questions[$questionID] = new Question($question);
}
foreach ($details->Groups as $groupID => $group) {
  $group->GroupID = $groupID;
  $groups[$groupID] = new Group($group, $questions);
}
foreach ($details->Pages as $pageID => $page) {
  $page->PageID = $pageID;
  $pages[$pageID] = new Page($page, $questions, $groups);
}

$pageOrder = [];
foreach ($details->PageOrder as $pageID) {
  if (isset($pages[$pageID])) {
    $pageOrder[] = $pages[$pageID];
  }
}

if ($SEGMENTS[2] == 'smallgroup') {
  $where = "AND DutyTeam = (SELECT DutyTeam FROM users WHERE UserID = ?)";
} else {
  $where = '';
}

$smallgroup = $SEGMENTS[2] == 'smallgroup';

// Find the responses
$query = "SELECT Name, UserID, Responses FROM questionnaire
          INNER JOIN `users` USING(UserID) WHERE Category = ? $where
          AND QuizId = ? ORDER BY Name ASC";
$stmt = $dbh->prepare($query);

if ($smallgroup) {
  $stmt->execute(['camper', $id, $username]);
} else {
  $stmt->execute(['camper', $id]);
}

$allResponses = [];
$allResponders = [];

while ($row = $stmt->fetch()) {
  $responses = json_decode($row['Responses']);
  $allResponders[$row['UserID']] = $people[$row['UserID']];
  foreach ($responses as $questionID => $answer) {
    if (!$answer) {
      continue;
    }
    if (!isset($allResponses[$questionID])) {
      $allResponses[$questionID] = [];
    }
    $allResponses[$questionID][$row['UserID']] = ["UserID" => $row['UserID'], "Answer" => $answer];
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
  foreach ($allResponders as $userID => $name) {
    $output .= "<tr>\n";
    $output .= "  <td style='white-space: nowrap;'>$name</td>\n";
    foreach ($details->FeedbackTable as $questionID) {

      /** @var Question $question */
      $question = $questions[$questionID];
      if (isset($allResponses[$questionID][$userID])) {
        $response = $allResponses[$questionID][$userID]['Answer'];
        $stringResponse = $question->getAnswerString($response);
        $other = "";
        if (isset($allResponses[$questionID."-other"][$userID]['Answer'])) {
          $other = "<br><small>".$allResponses[$questionID."-other"][$userID]['Answer']."</small>";
        }
        if ($stringResponse === Question::OTHER_RESPONSE) {
          $stringResponse = "Other";
        }
        $output .= "<td style='".$question->getSpecialStyle($response)."'>$stringResponse $other</td>\n";
      } else {
        $output .= "  <td>--</td>";
      }
    }
    $output .= "</tr>\n";
  }
  $output .= "</table>\n";
}

foreach ($pageOrder as $page) {
  $output .= "<h2>{$page->title}</h2>\n";
  foreach ($page->questions as $question) {
    if (isset($details->FeedbackTable) && $question instanceof Question
       && in_array($question->questionID, $details->FeedbackTable, true)) {
      continue;
    }
    $output .= $question->renderFeedback($allResponses, $people);
  }
}

$twig->addGlobal("id", $id);
$twig->addGlobal("output", $output);
$twig->addGlobal("smallgroup", $smallgroup);

fetch();