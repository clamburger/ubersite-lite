<?php
use Ubersite\Message;

if (count($people) === 0) {
  $twig->addGlobal('noUsers', true);
}

if (count($people) === 0) {
  $messages->addMessage(new Message('warning', 'There are currently no users in the database.'));
}


