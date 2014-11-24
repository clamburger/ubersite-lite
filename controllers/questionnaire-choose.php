<?php
$title = 'Choose Questionnaire';
$tpl->set('title', $title);

$stmt = $dbh->query('SELECT Id, Name FROM questionnaires');
$questionnaires = $stmt->fetchAll();

$redirect = isset($_GET['src']) ? $_GET['src'] : 'questionnaire';
$tpl->set('redirect', $redirect);

// Only one questionnaire exists, so automatically select it
if (count($questionnaires) === 1) {
  $id = $questionnaires[0]['Id'];
  header("Location: /$redirect/$id");
  exit;
}

$tpl->set('questionnaires', $questionnaires);
fetch();
