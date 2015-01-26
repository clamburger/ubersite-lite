<?php
use Ubersite\Message;
use Ubersite\Questionnaire;
use Ubersite\Utils;

// These will almost certainly be overidden.
$stage = 0;

// Which questionnaire.
$id = $SEGMENTS[1];

if (!$id) {
    header('Location: /choose?src=questionnaire');
    exit;
}

// Check that the questionnaire exists, and if it does, load up information about it
$stmt = $dbh->prepare('SELECT * FROM questionnaires WHERE Id = ?');
$stmt->execute([$id]);
if (!$row = $stmt->fetch()) {
    header('Location: /choose?src=questionnaire');
    exit;
}
$questionnaire = new Questionnaire($row);
$pages = $questionnaire->pages;

$twig->addGlobal('questionnaire', $questionnaire);
$twig->addGlobal('title', $questionnaire->getTitle());

$totalStages = count($pages);

// Get the current page for the user.
$stmt = $dbh->prepare('SELECT * FROM responses WHERE Username = ? AND QuizId = ?');
$stmt->execute([$user->Username, $id]);
if ($row = $stmt->fetch()) {
    $stage = intval($row['QuestionStage']);
    $currentData = json_decode($row['Responses'], true);
}

// Add a skeleton entry to the database
if ($SEGMENTS[2] == 'begin' && $row === false) {
    $query = "INSERT INTO responses (Username, QuizId, QuestionStage, Responses)
            VALUES (?, ?, 1, '{}')";
    $stmt = $dbh->prepare($query);
    $stmt->execute([$user->Username, $id]);
    $stage = 1;
}

// Delete current progress
if ($SEGMENTS[2] == 'delete' && $user->isLeader()) {
    $stmt = $dbh->prepare('DELETE FROM responses WHERE Username = ? AND `QuizId` = ?');
    $stmt->execute([$user->Username, $id]);
    $stage = 0;
    $messages->addMessage(new Message("success", "Hopes deleted."));
    header('Location: /questionnaire/' . $id);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['stage']) && $stage === intval($_POST['stage'])) {
        unset($_POST['stage']);
      // Merge the data that currently exists with the newly submitted data
        $responses = json_encode(array_merge($currentData, $_POST));
        $query = 'UPDATE responses SET Responses = ?, QuestionStage = ?
              WHERE QuizId = ? AND Username = ?';
        $stmt = $dbh->prepare($query);
        $stmt->execute([$responses, ++$stage, $id, $user->Username]);
        $messages->addMessage(new Message("success", $pages[$stage-2]->title." successfully submitted."));
        Utils::refresh();
    }
}

$twig->addGlobal("stage", $stage);
if ($stage > $totalStages) {
    $message = "Congratulations. The test is now over. ".
        "All Aperture technologies remain safely operational up to 4000 degrees Kelvin. ".
        "Rest assured that there is absolutely no chance of a dangerous equipment malfunction ".
        "prior to your victory candescence. Thank you for participating in this Aperture Science ".
        "computer-aided enrichment activity. Goodbye.";
    $messages->addMessage(new Message("alert", $message));
    $twig->addGlobal("end", true);
} elseif ($stage > 0) {
    $twig->addGlobal("title", $pages[$stage-1]->title);
    $twig->addGlobal("questions", $pages[$stage-1]->renderHTML());
}
