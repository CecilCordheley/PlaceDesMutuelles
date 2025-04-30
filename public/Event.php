<?php
use vendor\easyFrameWork\Core\Master\Cryptographer;
require_once ("vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

use vendor\easyFrameWork\Core\Master\Router;

use vendor\easyFrameWork\Core\Master\Autoloader;

EasyFrameWork::INIT();
Autoloader::register();
$router = new Router();
$router->addRoute("EventTest","EventController");
$router->addRoute("addEvent","EventController");
if(isset($_GET["act"]) && isset($_GET["id"])){
    $router->addRoute($_GET["act"]."_".$_GET["id"],"EventController");
}
//Ici Insérez les routes
$router->route($_SERVER["REQUEST_URI"],["year"=>date("Y")]);