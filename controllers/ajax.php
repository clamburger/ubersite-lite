<?php
use Ubersite\Message;
use Ubersite\Utils;

if (!$user->isLeader()) {
    Utils::send403($twig);
}

$action = $_POST['action'];

if ($action === 'duplicate-questionnaire') {
    $query = <<<SQL
      INSERT INTO questionnaires (Name, Pages, Intro)
      SELECT Name || ' (copy)', Pages, Intro FROM questionnaires WHERE Id = ?
SQL;
    $stmt = $dbh->prepare($query);
    $stmt->execute([$_POST['id']]);
    if ($stmt->rowCount() === 0) {
        $messages->addMessage(new Message('error', 'Questionnaire could not be duplicated. Invalid ID?'));
    } else {
        $messages->addMessage(new Message('success', 'Questionnaire successfully duplicated.'));
    }
    
} elseif ($action === 'delete-questionnaire') {
    $stmt = $dbh->prepare('DELETE FROM questionnaires WHERE Id = ?');
    $stmt->execute([$_POST['id']]);
    if ($stmt->rowCount() === 0) {
        $messages->addMessage(new Message('error', 'Questionnaire was not deleted. Invalid ID?'));
    } else {
        $messages->addMessage(new Message('success', 'Questionnaire successfully deleted.'));
    }
}
