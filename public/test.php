<?php
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\SQLFactory;
require_once ("vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

use vendor\easyFrameWork\Core\Master\Autoloader;
use SQLEntities\EventEntity;
use SQLEntities\ExchangeEntity;
use SQLEntities\IntMessageEntity;
//use Core\Master\Controller\HomeController;
EasyFrameWork::INIT();
EasyFrameWork::registerClass("Cryptographer",new Cryptographer());
//require_once "vendor/easyFrameWork/Core/Master/autoload.class.php";
//use Main\Main;
Autoloader::register();
/*Création d'un Echange */
/*
$message=new IntMessageEntity;
$message->Exchange=1;
$message->dateIntMessage=date("Y-m-d H:t:s");
$message->contentIntMessage="Bonjour";
$message->utilisateur_tbl_ID_USER="20";


IntMessageEntity::add(new SQLFactory(),$message);
$message=new IntMessageEntity;
$message->Exchange=1;
$message->dateIntMessage=date("Y-m-d H:t:s");
$message->contentIntMessage="Bonjour";
$message->utilisateur_tbl_ID_USER="19";


IntMessageEntity::add(new SQLFactory(),$message);*/
//$evts=EventEntity::getEventTblBy(new SQLFactory(),"ID_ORGA",3);
$exchanges=ExchangeEntity::getAll(new SQLFactory());
foreach($exchanges as $e){
    EasyFrameWork::Debug($e->getArray());
}