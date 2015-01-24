<?php
use Ubersite\Utils;

if (!$user->isLeader()) {
    Utils::send403($twig);
}

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
foreach ($details->Pages as $page) {
    $pages[] = $page->Title;
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
            $style = "text-align: center; background-color: orange; color: white;";
            $temp["stages"][] = "<td style='$style'>In Progress</td>";
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
