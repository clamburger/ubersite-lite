<?php
# This will both make it safe for input into the database and safe to output again.
function userInput($input) {
  $output = str_replace(array("<", ">", '"'), array("&lt;", "&gt;", "&quot;"), $input);
  return $output;
}

# <img src="data_url('filename.png', 'image/png');" />
function dataURI($file, $mime) {
  $contents = file_get_contents($file);
  $base64   = base64_encode($contents);
  return "data:$mime;base64,$base64";
}

function fetch($filename = false, $HTML = false) {
  global $tpl;
  global $PAGE;
  global $twig;
  global $messages;

  if (!$filename) {
    $filename = $PAGE;
  }
  if (!isset($tpl->scalars['contenttitle'])) {
    $tpl->set('contenttitle', $tpl->scalars['title']);
  }

  $tpl->set('messages', $messages->getAllMessageHTML());

  if ($HTML) {
    $tpl->set('content', $HTML);
  } else {
    if (file_exists("views/$filename.twig")) {
      $tpl->set('content', $twig->render("$filename.twig", $tpl->dump_vars()));
    } else {
      $tpl->set('content', $tpl->fetch("views/$filename.tpl"));
    }
  }

  $page = $tpl->fetch('views/master.tpl');
  // Clean up any extreneous <tag:s.
  echo preg_replace("/\<tag:[^\/]* \/>/", "", $page);
}

function refresh() {
  header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
  exit;
}
