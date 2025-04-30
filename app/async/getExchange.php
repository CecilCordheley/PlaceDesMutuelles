<?php

namespace Async;
use Async\asyncFunction;
use SQLEntities\ExchangeEntity;
//SQLEntities
use SQLEntities\ExchangeTbl;
use SQLEntities\UtilisateurEntity;

use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\SessionManager;

    class getExchange extends AsyncFunction{
        public function handle($args){
            parent::handle($args);
            $sessionManager=EasyGlobal::createSessionManager();
        $user=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
            if($user->TYPE_UTILISATEUR!="1"){
                if($user->ID_ORGA!=null){
                    $exchange=ExchangeEntity::getExchangeTblBy(new SQLFactory(),"Organisme",$user->ID_ORGA);
                }else{
                    $exchange=ExchangeEntity::getAll(new SQLFactory());
                   
                }
            }else
                if(isset($args["ID_USER"]))
                     $exchange=ExchangeEntity::getExchangeTblBy(new SQLFactory(),"Utilisateur",$args["ID_USER"]);
                else{
                    echo json_encode(["error"=>1,"message"=>"no parameters"]);
                    return;
                }
             //   EasyFrameWork::Debug($exchange);
             if(isset($args["act"])){
            
                switch($args["act"]){
                    case "getMessage":{
                        $exchange=ExchangeEntity::getExchangeTblBy(new SQLFactory(),"uuidExchange",$args["UUID"]);
                        $m=$exchange->getMessages(new SQLFactory());
                        if(gettype($m)!="boolean"){
                            if(gettype($m)=="array"){
                            $i=0;
                            echo json_encode(["result"=>"ok","data"=>array_reduce($m,function($car,$el) use(&$i,$user){
                                $car[$i]=$el->getArray();
                                $car[$i]["UTILISATEUR"]=$user->PSEUDO_USER;
                                $i++;
                                return $car;
                            },[])]);
                        }else{
                                echo json_encode(["result"=>"ok","data"=>[$m->getArray()]]);
                            }
                        }else{
                            echo json_encode(["error"=>2,"message"=>"no messages"]);
                        }
                        break;
                    }
                    case "addMessage":{
                        $post = json_decode(file_get_contents('php://input'), true);
                        $exchange=ExchangeEntity::getExchangeTblBy(new SQLFactory(),"uuidExchange",$args["UUID"]);
                        if($exchange->addMessage(new SQLFactory(),$user,$post["message"])){
                            echo json_encode(["result"=>"ok"]);
                        }else{
                            echo json_encode(["error"=>"2"]);
                        }
                        break;
                    }
                    case "nextExchange":{
                        $i=0;
                        echo json_encode(["result"=>"ok","data"=>array_reduce($exchange,function($car,$el) use(&$i){
                            $el->HeureDebut = trim($el->HeureDebut);
                            $ElTime = strtotime(date("Y-m-d") . " " .trim($el->HeureDebut)); // Associez date + heure
                            $currentTime = strtotime(date("Y-m-d H:i:s")); // Associez date + heure actuelle
                         //   echo("Comparing: ElTime=" . date("Y-m-d H:i:s", $ElTime) . " with CurrentTime=" . date("Y-m-d H:i:s", $currentTime));
                            if ($el->dateExchange == date("Y-m-d") && $ElTime > $currentTime){
                           //     EasyFrameWork::Debug($el->getArray());
                                $car[$i]=$el->getArray();
                                $car[$i]["Login"]=$car[$i]["UtilisateurEntity"]["PSEUDO_USER"];
                            $i++;
                            }
                            return $car;
                        },[])]);
                        break;
                    }
                }
            }else
                echo json_encode(["result"=>"ok","data"=>$exchange]);
        }
    }