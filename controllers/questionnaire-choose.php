<?php
$stmt = $dbh->query('SELECT Id, Name FROM questionnaires');
$questionnaires = $stmt->fetchAll();

$redirect = isset($_GET['src']) ? $_GET['src'] : 'questionnaire';
$twig->addGlobal('redirect', $redirect);

// Only one questionnaire exists, so automatically select it
if (count($questionnaires) === 1) {
  $id = $questionnaires[0]['Id'];
  header("Location: /$redirect/$id");
  exit;
}

$twig->addGlobal('questionnaires', $questionnaires);
