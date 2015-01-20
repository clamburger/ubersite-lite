<?php
// Which questionnaire.
$id = $SEGMENTS[1];

if (!$id) {
  header('Location: /choose?src=editor');
  exit;
}

if ($id === 'new') {
  $query = <<<SQL
    INSERT INTO questionnaires (Name, Pages, Intro) VALUES (
      'Untitled Questionnaire', '{"Questions": {}, "Groups": {}, "Pages": {}, "PageOrder": []}', ''
    );
SQL;

  $dbh->exec($query);
  header('Location: /editor/' . $dbh->lastInsertId());
  exit;
}

// Check that the questionnaire exists, and if it does, load up information about it
$stmt = $dbh->prepare('SELECT * FROM questionnaires WHERE Id = ?');
$stmt->execute([$id]);
if (!$row = $stmt->fetch()) {
  header('Location: /choose?src=editor');
  exit;
}

$title = $row['Name'];
$twig->addGlobal('title', $title);
