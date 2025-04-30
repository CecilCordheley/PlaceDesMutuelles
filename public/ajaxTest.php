<?php
require_once ("vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\Autoloader;
use Async\getExchange;
use Async\AsyncExchange;
use Async\AsyncUser;
use Async\AsyncMessage;
EasyFrameWork::INIT();
date_default_timezone_set('Europe/Paris');
switch($_GET["act"]){
    case "addExchangeMessage":{
        $async=new AsyncExchange();
        $async->handle(["uuidExchange"=>$_GET["uuidExchange"],
    "user"=>$_GET["ID_USER"],
"message"=>$_GET["message"]],"addMessage");
        break;

    }
    case "getExchangeMessage":{
        $async=new AsyncExchange();
        $async->handle(["uuidExchange"=>$_GET["uuidExchange"]],"getMessages");
        break;
    }
    case "toolgeDisplayMessage":{
        $async=new AsyncMessage();
        $async->handle(["id"=>$_GET["id"]],"toogleMessage");
        break;
    }
    case "getUser":{
        $async=new AsyncUser();
        $async->handle(["id"=>$_GET["id"]],"getUser");
        break;
    }
    case "banUser":{
        $jsonData = json_decode(file_get_contents('php://input'),true);
        $async=new AsyncUser();
        $comment=$jsonData["comment"];
        $async->handle(["id"=>$_GET["id"],"comment"=>$jsonData["comment"]],"banUser");
        break;
    }
}
/*
$async=new getExchange();
$async->handle($_GET);*/