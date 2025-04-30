<?php
require __DIR__ . '/vendor/easyFrameWork/Core/Master/Autoloader.php';
use vendor\easyFrameWork\Core\Master\Cryptographer;
require_once ("vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

use vendor\easyFrameWork\Core\Master\Router;

use vendor\easyFrameWork\Core\Master\Autoloader;

//EasyFrameWork::INIT();
//Autoloader::register();
parse_str(implode('&', array_slice($argv, 1)), $_GET);