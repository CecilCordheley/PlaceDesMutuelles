<?php
use vendor\easyFrameWork\Core\Master\Cryptographer;
require_once ("vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

use vendor\easyFrameWork\Core\Master\Router;

use vendor\easyFrameWork\Core\Master\Autoloader;
use vendor\easyFrameWork\Core\Master\SQLtoView;
//use Core\Master\Controller\HomeController;
EasyFrameWork::INIT();
EasyFrameWork::registerClass("Cryptographer",new Cryptographer());
//require_once "vendor/easyFrameWork/Core/Master/autoload.class.php";
//use Main\Main;
Autoloader::register();
$router = new Router();
$router->addRoute('SujetAll', 'SujetController');
$router->addRoute('NouveauSujet', 'SujetController');
$router->addRoute('addSujet', 'SujetController');
$router->addRoute('MesSujets', 'SujetController');
if(isset($_GET["id"])){
    if(isset($_GET["act"])&& $_GET["act"]=="close"){
        $router->addRoute("closeSubject-".$_GET['id'], 'SujetController');
    }
    if(isset($_GET["lib"]))
        $router->addRoute("sujet-".$_GET['id']."-".$_GET['lib'].".html", 'SujetController');
    $router->addRoute("POSTMessage_".$_GET["id"],"SujetController");
}
$router->route($_SERVER["REQUEST_URI"],["year"=>date("Y"),"date_jour"=>date("Y-m-d")]);