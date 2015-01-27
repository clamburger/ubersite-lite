<?php
use Ubersite\Questionnaire;

$questionnaires = Questionnaire::loadAllFromDatabase();

$redirect = isset($_GET['src']) ? $_GET['src'] : 'questionnaire';

// Only one questionnaire exists, so automatically select it
if (count($questionnaires) === 1) {
    $id = $questionnaires[0]->id;
    header("Location: /$redirect/$id");
    exit;
}

$twig->addGlobal('redirect', $redirect);
$twig->addGlobal('questionnaires', $questionnaires);
