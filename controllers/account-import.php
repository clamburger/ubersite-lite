<?php
use Ubersite\Message;
use Ubersite\Utils;

$fileUploadErrors = [
  0 => 'The file was uploaded successfully.',
  1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
  2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
  3 => 'The uploaded file was only partially uploaded.',
  4 => 'No file was uploaded.',
  6 => 'Missing a temporary folder.',
  7 => 'Failed to write file to disk.',
  8 => 'A PHP extension stopped the file upload.'
];

if (count($people) === 0) {
  $twig->addGlobal('noUsers', true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_FILES['file']['error']) {
    $messages->addMessage(new Message('error', 'File upload error: '
      . $fileUploadErrors[$_FILES['file']['error']]));
  } else {
    $messages->addMessage(new Message('success', 'nothing happened'));
    Utils::refresh();
  }
}
