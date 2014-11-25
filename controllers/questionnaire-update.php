<?php
use Ubersite\Message;

$title = 'Synchronise Questionnaire Tables';
$twig->addGlobal('title', $title);

$id = $SEGMENTS[1];

if (!$id) {
  header('Location: /questionnaire-choose?src=/questionnaire-update');
  exit;
}

// Check that the questionnaire exists, and if it does, load up information about it
$stmt = $dbh->prepare('SELECT * FROM questionnaires WHERE Id = ?');
$stmt->execute([$id]);
if (!$row = $stmt->fetch()) {
  header('Location: /questionnaire-choose?src=/questionnaire-update');
  exit;
}

$details = json_decode($row['Pages'], true);
$page = &$details['Pages']['elective-feedback'];

$groups = $page['Questions'];

$columns = [];
foreach ($groups as $ID) {
  if ($ID == 'electives') {
    continue;
  }
  $columns[$ID] = [true, false, false];
}

// Now cross-reference with the electives table.
$query = 'SELECT ShortName, LongName FROM questionnaire_electives ORDER BY Sorting ASC';
$stmt = $dbh->query($query);

while ($row = $stmt->fetch()) {
  if (isset($columns[$row['ShortName']])) {
    $columns[$row['ShortName']][1] = true;
    $columns[$row['ShortName']][2] = $row['LongName'];
  } else {
    $columns[$row['ShortName']] = [false, true, $row['LongName']];
  }
}

$columnHTML = [];
$add = [];
$remove = [];

// Check if any questionnaires have been submitted
$stmt = $dbh->prepare('SELECT COUNT(*) FROM questionnaire_responses WHERE QuizId = ?');
$stmt->execute([$id]);
$count = $stmt->fetch()[0];

// Generate the HTML for the table
foreach ($columns as $ID => $data) {
  $HTML = "<td>$ID</td>\n";
  if ($data[2]) {
    $HTML .= "<td>{$data[2]}</td>\n";
  } else {
    $HTML .= "<td>---</td>\n";
  }
  if (!$data[0]) {
    $add[] = [$ID, $data[2]];
    $HTML .= "<td style='color: white; background-color: red; text-align: center;'>New elective: needs to be added</td>";
  } else if (!$data[1]) {
    $remove[] = $ID;
    $HTML .= "<td style='color: white; background-color: orange; text-align: center;'>Old elective: can be removed</td>";
  } else {
    $HTML .= "<td style='color: white; background-color: green; text-align: center;'>Present in both tables</td>";
  }

  $columnHTML[] = $HTML;
}

// Make changes to the `questionnaire` table.
if (isset($_POST['submit'])) {

  // Adding new electives
  if ($_POST['submit'] == 'Add New Electives' && count($add) > 0) {

    $query = "ALTER TABLE questionnaire_responses";
    $newQuestions = [];
    $newGroups = [];
    foreach ($add as $info) {
      $ID = $info[0];
      $name = $info[1];
      $newQuestions["$ID-1"] = [
        "Question" => "How much did you enjoy the $name sessions?",
        "AnswerType" => "1-5"
      ];
      $newQuestions["$ID-2"] = [
        "Question" => "How much did you learn from the sessions?",
        "AnswerType" => "1-5"
      ];
      $newGroups[$ID] = [
        "Title" => $name,
        "Questions" => ["$ID-1", "$ID-2"],
        "Collapsible" => true
      ];
    }

    $details['Questions'] = array_merge($newQuestions, $details['Questions']);
    $details['Groups'] = array_merge($newGroups, $details['Groups']);
    $page['Questions'] = array_merge(array_keys($newGroups), $page['Questions']);

    $details = json_encode($details, JSON_PRETTY_PRINT);
    $stmt = $dbh->prepare('UPDATE questionnaires SET Pages = ? WHERE Id = ?');
    $stmt->execute([$details, $id]);

    $messages->addMessage(new Message("success",
      "The new electives were succesfully added to the <code>`questionnaire`</code> table."));
    refresh();

  # Removing old electives
  } else if ($_POST['submit'] == "Remove Old Electives" && count($remove) > 0) {
    if ($count) {
      $messages->addMessage(new Message("error",
        "You can't remove old electives with rows still in the <code>`questionnaire`</code> table!"));
    } else {
      foreach ($remove as $ID) {
        unset($details["Questions"]["$ID-1"]);
        unset($details["Questions"]["$ID-2"]);
        unset($details["Groups"]["$ID"]);
        $key = array_search($ID, $page['Questions']);
        unset($page['Questions'][$key]);
      }

      $details = json_encode($details, JSON_PRETTY_PRINT);
      $stmt = $dbh->prepare('UPDATE questionnaires SET Pages = ? WHERE Id = ?');
      $stmt->execute([$details, $id]);
      $messages->addMessage(new Message('success',
        'The old electives were succesfully removed from the Elective Feedback page.')
      );
      refresh();
    }
  }
  unset($_POST);
}

$twig->addGlobal('columns', $columnHTML);

$HTML = '';

// Generate the HTML for changes that need to be made
if (count($add) === 0 && count($remove) === 0) {
  $HTML .= 'Both tables are currently in sync: no changes need to be made.';
} else {
  if (count($add)) {
    $HTML .= 'The following electives are not present in the <tt>`questionnaire`</tt> table.\n';
    $HTML .= "<ul style='margin: 0px;'>\n";
    foreach ($add as $ID) {
      $HTML .= "\t<li>{$ID[1]}</li>\n";
    }
    $HTML .= "</ul>\n";
    $HTML .= "<input type=\"submit\" name=\"submit\" value=\"Add New Electives\" style=\"font-size: 150%;\" /><br /><br />";
  }
  if (count($remove)) {
    $HTML .= "The following electives no longer need to be on the Elective Feedback page.";
    if ($count) {
      $HTML .= " They cannot be removed until the <tt>`questionnaire`</tt> table is empty.";
    }
    $HTML .= "\n<ul style='margin: 0px;'>\n";
    foreach ($remove as $ID) {
      $HTML .= "\t<li>$ID</li>\n";
    }
    $HTML .= "</ul>\n";
    $HTML .= "<input type=\"submit\" name=\"submit\" value=\"Remove Old Electives\" style=\"font-size: 150%;\" ";
    if ($count) {
      $HTML .= "disabled=\"disabled\"";
    }
    $HTML .= "/><br /><br />";
  }
}

$twig->addGlobal('actions', $HTML);

fetch();
