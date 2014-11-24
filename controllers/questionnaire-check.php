<?php
$title = 'Questionnaire Status';
$tpl->set('title', $title);

$id = $SEGMENTS[1];

if (!$id) {
  header('Location: /questionnaire-choose?src=questionnaire-check');
  exit;
}

// Check that the questionnaire exists, and if it does, load up information about it
$stmt = $dbh->prepare('SELECT * FROM questionnaires WHERE Id = ?');
$stmt->execute([$id]);
if (!$row = $stmt->fetch()) {
  header('Location: /questionnaire-choose?src=questionnaire-check');
  exit;
}

$details = json_decode($row['Pages']);
$pages = [];
foreach ($details->PageOrder as $pageID) {
  if (isset($details->Pages->$pageID)) {
    $pages[] = $details->Pages->$pageID->Title;
  }
}

$tpl->set("pages", $pages, false);

$rawStatus = [];

// Fetch the latest stage for each camper
$query = 'SELECT UserID, QuestionStage FROM users LEFT JOIN questionnaire USING(UserID)
          WHERE (QuizId = ? OR QuizId IS NULL) AND Category = ?';
$stmt = $dbh->prepare($query);
$stmt->execute([$id, 'camper']);
while ($row = $stmt->fetch()) {
  $stage = intval($row['QuestionStage']);
  $rawStatus[$row['UserID']] = $stage;
}

$status = [];
$totals = array_fill(1, count($pages), 0);
foreach ($rawStatus as $userID => $userStatus) {
  $temp = array("name" => $people[$userID]->Name);
  for ($i = 1; $i <= count($pages); $i++) {
    if ($i > $userStatus) {
      $temp["stages"][] = "<td style='text-align: center;'>---</td>";
    } else if ($i === $userStatus) {
      $temp["stages"][] = "<td style='text-align: center; background-color: orange; color: white;'>In Progress</td>";
    } else {
      $temp["stages"][] = "<td style='text-align: center; background-color: green; color: white;'>Complete</td>";
      $totals[$i]++;
    }
  }
  $status[] = $temp;
}

foreach ($totals as $key => $total) {
  $totals[$key] = "$total / ".count($rawStatus);
}

$tpl->set('status', $status, false);
$tpl->set('totals', $totals, false);
$tpl->set('head', '<meta http-equiv="refresh" content="5;/questionnaire-check/'.$id.'?autorefresh" >');

fetch();
