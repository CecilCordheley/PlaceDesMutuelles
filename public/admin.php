<?php
use vendor\easyFrameWork\Core\Master\Cryptographer;
require_once ("vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

use vendor\easyFrameWork\Core\Master\Router;

use vendor\easyFrameWork\Core\Master\Autoloader;

//use Core\Master\Controller\HomeController;
EasyFrameWork::INIT();
EasyFrameWork::registerClass("Cryptographer",new Cryptographer());
//require_once "vendor/easyFrameWork/Core/Master/autoload.class.php";
//use Main\Main;
Autoloader::register();
$router = new Router();
$router->addRoute('UserGestion', 'AdminController');
$router->addRoute('SujetGestion', 'AdminController');
$router->addRoute('ExchangeGestion', 'AdminController');
if(isset($_GET["act"])){
    if(isset($_GET["id"])){
        $router->addRoute('UserGestion-'.$_GET["act"]."_".$_GET["id"], 'AdminController');
        $router->addRoute('sujetGestion-'.$_GET["act"]."_".$_GET["id"], 'AdminController');
    }else{
        $router->addRoute('UserGestion-'.$_GET["act"], 'AdminController');
        $router->addRoute('sujetGestion-'.$_GET["act"], 'AdminController');
        $router->addRoute("ExchangeGestion-".$_GET["act"],"AdminController");
    }
}
if(isset($_GET["id"])){
    $router->addRoute('sujetGestion-'.$_GET["id"], 'AdminController');
}
$router->route($_SERVER["REQUEST_URI"],["year"=>date("Y")]);