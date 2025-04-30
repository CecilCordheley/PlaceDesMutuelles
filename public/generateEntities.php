<?php
require_once ("vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
use vendor\easyFrameWork\Core\Master\EasyFrameWork;


use vendor\easyFrameWork\Core\Master\SqlEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\EventTbl;
use SQLEntities\ExchangeTbl;
use SQLEntities\IntMessageTbl;
//use Core\Master\Controller\HomeController;
EasyFrameWork::INIT();
$sqlF=new SQLFactory();
$tables=["planningTbl","OrganismeTbl"];
foreach($tables as $t){
    SqlEntities::TblClassToEntity($sqlF,$t);
}
