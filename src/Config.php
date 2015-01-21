<?php
namespace Ubersite;

class Config
{
  const CONFIG_FILE = 'config/config.php';

  private $loaded = false;
  private $config;

  public function __construct()
  {
    // Add the root of the website to the include path to make including files easier
    set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');

    if (!file_exists(self::CONFIG_FILE)) {
      return;
    }

    $this->config = (require self::CONFIG_FILE);
    $this->loaded = true;
  }

  /**
   * @return bool If the config file was successfully loaded
   */
  public function isLoaded()
  {
    return $this->loaded;
  }

  public function getMenu()
  {
    return $this->config['menu'];
  }

  public function getCampName()
  {
    return $this->config['campName'];
  }
}
