<?php
/* Ici les entités SQL */
use SQLEntities\ExchangeEntity;
use SQLEntities\MessageEntity;
use SQLEntities\MessageTbl; 
use SQLEntities\PlanningEntity;
use SQLEntities\UtilisateurEntity;
use SQLEntities\OrganismeTbl;
/*ici les classes*/
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\Query;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Main;
require_once ("vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

use vendor\easyFrameWork\Core\Master\Router;

use vendor\easyFrameWork\Core\Master\Autoloader;

//use Core\Master\Controller\HomeController;
EasyFrameWork::INIT();
if(isset($_GET["act"])){
    switch($_GET["act"]){
        case "getSlotExchange":{
            if(!isset($_GET["day"])){
                echo json_encode(["error"=>0,"message"=>"no day parameters"]);
                return;
            }
            try{
            $sqlF=new SQLFactory();
            $sessionManager=EasyGlobal::createSessionManager();
            $user=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
            $organisme=$user->getOrganisme($sqlF);
            $planningDataWithSlots = $organisme->getPlanningDataWithSlots(new SQLFactory(), 10);
        $planningData = $planningDataWithSlots["planning"];
        $occupedSlots = $planningDataWithSlots["occuped"];
        echo json_encode(["result"=>"OK","data"=>["planningData"=>$planningData[$_GET["day"]],"occupedSlots"=>$occupedSlots[$_GET["day"]]]]);
            }catch(Exception $e){
                echo json_encode(["error"=>1,"message"=>$e->getMessage()]);
            }
            break;
        }
        case "reserveExchange":{
            $sqlF=new SQLFactory();
            $sessionManager=EasyGlobal::createSessionManager();
            $user=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
            $post = json_decode(file_get_contents('php://input'), true);
            $exchanges=new ExchangeEntity;
            $exchanges->uuidExchange=uniqid();
            $exchanges->dateExchange=$post["date"];
            $exchanges->HeureDebut=str_replace("-",":",$post["heure"]);
            $exchanges->Utilisateur=$user->ID_USER;
            $exchanges->Organisme=$user->ID_ORGA;
            $exchanges->StateExchange=0;
           // EasyFrameWork::Debug($exchanges);
            ExchangeEntity::add($sqlF,$exchanges,function(){
                echo json_encode(["result"=>"OK"]);
            });
            break;
        }
        case "getMessage":{
            $m=MessageEntity::getMessageTblBy(new SQLFactory(),"ID_SUJET",$_GET["id"]);
            if($m){
                $i=0;
                $return =(gettype($m)!="array")?$m->getArray():array_reduce($m,function($c,$e) use(&$i){
                    $c[$i]=$e->getArray();
                   $u=$e->getUser(new SQLFactory());
                    $c[$i]["USER"]=$u->getArray();
                    $i++;
                    return $c;
                },[]);
                echo json_encode(["result"=>"OK","data"=>$return]);
            }else{
                echo json_encode(["result"=>"KO","error"=>"no messages"]);
            }
            break;
        }
    }
}