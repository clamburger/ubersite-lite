<?php
use League\Csv\Reader;
use Ubersite\DatabaseManager;
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

// Allow access if there are no users in the database
if (count($people) === 0) {
  $twig->addGlobal('noUsers', true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_FILES['csv']['error']) {
    $messages->addMessage(new Message('error', 'File upload error: '
      . $fileUploadErrors[$_FILES['csv']['error']]));
    Utils::refresh();
  } else {
    $reader = Reader::createFromFileObject(new SplFileObject($_FILES['csv']['tmp_name']));
    $reader->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);
    $error = false;

    // Use a transaction, so that if any of the rows fail to process the whole thing is cancelled.
    $dbh = DatabaseManager::get();
    $dbh->beginTransaction();

    $stmt = $dbh->prepare('REPLACE INTO users VALUES(?, ?, ?, ?, ?)');

    foreach ($reader as $index => $row) {
      // Check that the row has the right number of columns
      if (count($row) !== 5) {
        $message = sprintf("Row %d has the wrong number of columns (expected 5, got %d)",
          $index + 1, count($row));
        $messages->addMessage(new Message('error', $message));
        $messages->addMessage(new Message(
          'error', 'Import failed. No database changes have been made.'
        ));
        $dbh->rollBack();
        $error = true;
        break;
      }

      // Handle default values for role, password and duty team
      list($username, $fullName, $role, $password, $dutyTeam) = $row;
      $role = ($role === '' ? 'camper' : strtolower($role));
      $password = ($password === '' ? null : password_hash($password, PASSWORD_DEFAULT));
      $dutyTeam = ($dutyTeam === '' ? null : $dutyTeam);

      $stmt->execute([$username, $fullName, $role, $dutyTeam, $password]);
    }

    // If there were no errors, commit the transaction.
    if (!$error) {
      $dbh->commit();
      $rows = count($reader->fetchAll());
      $message = sprintf('Success! %d user%s were imported.', $rows, $rows === 1 ? '' : 's');
      $messages->addMessage(new Message('success', $message));

      // Redirect to the login page if this was the initial import, otherwise just refresh
      if (count($people) === 0) {
        header('Location: /');
        exit;
      } else {
        Utils::refresh();
      }
    }
  }
}
