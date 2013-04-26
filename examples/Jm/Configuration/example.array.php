<?php

require_once 'Jm/Autoloader.php';
Jm_Autoloader::singleton()
  ->prependPath(dirname(__FILE__) . '/../../../lib/php');

$conf = new Jm_Configuration_Array(array(
    'a' => 1,
    'b' => 'foo',
    'd' => new DateTime()
));

var_dump($conf->getAll());
