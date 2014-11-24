<?php
# <img src="data_url('filename.png', 'image/png');" />
function dataURI($file, $mime) {
  $contents = file_get_contents($file);
  $base64   = base64_encode($contents);
  return "data:$mime;base64,$base64";
}

function fetch() {
  global $PAGE;
  global $twig;
  global $messages;

  $twig->addGlobal('messages', $messages->getAllMessageHTML());
  echo $twig->render("$PAGE.twig");
}

function refresh() {
  header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
  exit;
}
