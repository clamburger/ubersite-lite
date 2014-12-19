<?php
namespace Ubersite;

/**
 * A collection of useful static functions.
 */
class Utils {
  /**
   * Encodes the contents of a file into base64 so that it can be directly embedded into a page.
   * Example use: <img src="dataURI('filename.png', 'image/png');">
   * @param $file string The location of the file
   * @param $mime string The MIME type of the file
   * @return string A data URI with the encoded contents of the file
   */
  public static function dataURI($file, $mime) {
    $contents = file_get_contents($file);
    $base64   = base64_encode($contents);
    return "data:$mime;base64,$base64";
  }

  /**
   * A redirect that just reloads the page.
   */
  public static function refresh() {
    header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
    exit;
  }

} 
