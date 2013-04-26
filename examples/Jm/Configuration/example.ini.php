<?php

require_once 'Jm/Autoloader.php';
Jm_Autoloader::singleton()
  ->prependPath(dirname(__FILE__) . '/../../../lib/php');

// define constants. They can be used in the ini file
define ('FLAG_1', 1);
define ('FLAG_2', 2);

// create a configuration instance
$configuration = new Jm_Configuration_Inifile('example.ini');

var_dump($configuration->getAll());
