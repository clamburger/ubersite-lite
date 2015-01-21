<?php
$id = $SEGMENTS[1];

if (!$id) {
  header('Location: /choose?src=progress');
  exit;
}

// Check that the questionnaire exists, and if it does, load up information about it
$stmt = $dbh->prepare('SELECT * FROM questionnaires WHERE Id = ?');
$stmt->execute([$id]);
if (!$row = $stmt->fetch()) {
  header('Location: /choose?src=progress');
  exit;
}

$details = json_decode($row['Pages']);
$pages = [];
foreach ($details->PageOrder as $pageID) {
  if (isset($details->Pages->$pageID)) {
    $pages[] = $details->Pages->$pageID->Title;
  }
}

$twig->addGlobal("pages", $pages);

$rawStatus = [];

// Fetch the latest stage for each camper
$query = "SELECT Username, QuestionStage FROM users LEFT JOIN responses USING(Username)
          WHERE (QuizId = ? OR QuizId IS NULL) AND Role = 'camper'";
$stmt = $dbh->prepare($query);
$stmt->execute([$id]);
while ($row = $stmt->fetch()) {
  $stage = intval($row['QuestionStage']);
  $rawStatus[$row['Username']] = $stage;
}

$status = [];
$totals = array_fill(1, count($pages), 0);
foreach ($rawStatus as $username => $userStatus) {
  $temp = array("name" => $people[$username]->Name);
  for ($i = 1; $i <= count($pages); $i++) {
    if ($i > $userStatus) {
      $temp["stages"][] = "<td style='text-align: center;'>---</td>";
    } elseif ($i === $userStatus) {
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

$twig->addGlobal('status', $status);
$twig->addGlobal('totals', $totals);
$twig->addGlobal('head', '<meta http-equiv="refresh" content="5;/progress/'.$id.'?autorefresh" >');
