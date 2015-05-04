<?php
use Ubersite\Message;
use Ubersite\Questionnaire;
use Ubersite\Questionnaire\Question;
use Ubersite\Utils;

if (!$user->isLeader()) {
    Utils::send403($twig);
}

$action = $_POST['action'];

if ($action === 'create-questionnaire') {
    $query = "INSERT INTO questionnaires (Name, Pages, Intro) VALUES ('Untitled Questionnaire', '[]', '')";
    $dbh->exec($query);
    exit;
} elseif ($action === 'delete-user') {
    $stmt = $dbh->prepare('DELETE FROM users WHERE Username = ?');
    $stmt->execute([$_POST['username']]);
    $messages->addMessage(
        new Message("success", "You have successfully deleted {$people[$_POST['username']]->name}'s account.")
    );
    exit;
} elseif ($action === 'change-user-name') {
    $stmt = $dbh->prepare('UPDATE users SET Name = ? WHERE Username = ?');
    $stmt->execute([$_POST['name'], $_POST['username']]);
    exit;
} elseif ($action === 'change-user-smallgroup') {
    $stmt = $dbh->prepare('UPDATE users SET SmallGroup = ? WHERE Username = ?');
    $stmt->execute([$_POST['smallGroup'], $_POST['username']]);
    exit;
} elseif ($action === 'change-user-role') {
    $stmt = $dbh->prepare('UPDATE users SET Role = ? WHERE Username = ?');
    $stmt->execute([$_POST['role'], $_POST['username']]);
    exit;
} elseif ($action === 'change-password') {
    $stmt = $dbh->prepare('UPDATE users SET Password = ? WHERE Username = ?');
    $password = $_POST['password'];
    if ($password === '') {
        $password = $_POST['username'];
    }
    $password = password_hash($password, PASSWORD_DEFAULT);
    $stmt->execute([$password, $_POST['username']]);
    exit;
}

$questionnaire = Questionnaire::loadFromDatabase($_POST['id']);
if ($questionnaire === null) {
    $messages->addMessage(new Message('error', "Couldn't load questionnaire with ID {$_POST['id']}."));
    exit;
}

if ($action === 'duplicate-questionnaire') {
    $query = <<<SQL
      INSERT INTO questionnaires (Name, Pages, Intro)
      SELECT Name, Pages, Intro FROM questionnaires WHERE Id = ?
SQL;
    $stmt = $dbh->prepare($query);
    $stmt->execute([$_POST['id']]);
    echo $dbh->lastInsertId();

} elseif ($action === 'delete-questionnaire') {
    $questionnaire->deleteQuestionnaire();

} elseif ($action === 'update-title') {
    $questionnaire->setTitle($_POST['text']);

} elseif ($action === 'update-intro') {
    $questionnaire->setIntro($_POST['text']);

} elseif ($action === 'duplicate-page') {
    $questionnaire->duplicatePage($_POST['page']);

} elseif ($action === 'delete-page') {
    $questionnaire->deletePage($_POST['page']);

} elseif ($action === 'move-page') {
    $questionnaire->movePage($_POST['page'], (int)$_POST['newPosition']);

} elseif ($action === 'create-page') {
    $questionnaire->createNewPage();

} elseif ($action === 'update-page-title') {
    $questionnaire->getPage($_POST['page'])->title = $_POST['text'];
    $questionnaire->updateDatabase();

} elseif ($action === 'update-page-intro') {
    $questionnaire->getPage($_POST['page'])->intro = $_POST['text'];
    $questionnaire->updateDatabase();

} elseif ($action === 'add-section') {
    $questionnaire->getPage($_POST['page'])->addSection();
    $questionnaire->updateDatabase();

} elseif ($action === 'duplicate-section') {
    $questionnaire->getPage($_POST['page'])->duplicateSection($_POST['section']);
    $questionnaire->updateDatabase();

} elseif ($action === 'delete-section') {
    $questionnaire->getPage($_POST['page'])->deleteSection($_POST['section']);
    $questionnaire->updateDatabase();

} elseif ($action === 'update-section-title') {
    $questionnaire->getPage($_POST['page'])->getSection($_POST['section'])->title = $_POST['text'];
    $questionnaire->updateDatabase();

} elseif ($action === 'section-collapsible') {
    $questionnaire->getPage($_POST['page'])->getSection($_POST['section'])->collapsible = (bool)$_POST['value'];
    $questionnaire->updateDatabase();

} elseif ($action === 'move-section') {
    $questionnaire->getPage($_POST['page'])->moveSection($_POST['section'], (int)$_POST['movement']);
    $questionnaire->updateDatabase();

} elseif ($action === 'delete-question') {
    $questionnaire->getPage($_POST['page'])->getSection($_POST['section'])->deleteQuestion($_POST['question']);
    $questionnaire->updateDatabase();

} elseif ($action === 'add-question') {
    $id = $questionnaire->getUnusedQuestionId($_POST['question']);

    $question = new Question($id);
    $question->question = $_POST['question'];
    $question->setAnswerType($_POST['answerType']);
    if (isset($_POST['answerOptions'])) {
        $question->answerOptions = $_POST['answerOptions'];
    }

    $questionnaire->getPage($_POST['page'])->getSection($_POST['section'])->addQuestion($question);
    $questionnaire->updateDatabase();

}
