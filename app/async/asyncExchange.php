<?php
namespace Async;
use SQLEntities\ExchangeEntity;
use SQLEntities\IntMessageEntity;
use Async\asyncFunction;
use SQLEntities\UtilisateurEntity;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\SQLFactory;

class AsyncExchange extends asyncFunction{
    public function handle($args,$act=null){
        parent::handle($args);
        switch($act){
            case "addMessage":{
                if(!isset($args["uuidExchange"])){
                    echo json_encode(["error"=>1,"message"=>"no UUID Exchange parameters"]);
                    return;
                }
                if(!isset($args["user"])){
                    echo json_encode(["error"=>1,"message"=>"no user parameters"]);
                    return;
                }
                if(!isset($args["message"])){
                    echo json_encode(["error"=>1,"message"=>"no message parameters"]);
                    return;
                }
                $sqlF=new SQLFactory();
                //Récupérer l'echange
                $echange=ExchangeEntity::getExchangeTblBy($sqlF,"uuidExchange",$args["uuidExchange"]);
                if($echange==false){
                    echo json_encode(["error"=>2,"message"=>"Exchange ".$args["uuidExchange"]." doesn't exist"]);
                    return;
                }
                $user=UtilisateurEntity::getUtilisateurTblBy($sqlF,"ID_USER",$args["user"]);
                if($user==false){
                    echo json_encode(["error"=>2,"message"=>"User ".$args["user"]." doesn't exist"]);
                    return;
                }
                if($echange->addMessage($sqlF,$user,$args["message"])==false){
                    echo json_encode(["error"=>3,"message"=>"Une erreur s'est produite"]);
                    return;
                }
                $echange->StateExchange=1;
                ExchangeEntity::update(new SQLFactory(),$echange);
                echo json_encode(["result"=>"ok","message"=>"message envoyé"]);
                    
                break;
            }
            case "getMessages":{
                if(!isset($args["uuidExchange"])){
                    echo json_encode(["error"=>1,"message"=>"no UUID Exchange parameters"]);
                    return;
                }
                $echanges=ExchangeEntity::getExchangeTblBy(new SQLFactory(),"uuidExchange",$args["uuidExchange"]);
                if($echanges==false){
                    echo json_encode(["error"=>2,"message"=>"wrong uuidExchange ".$args["uuidExchange"]]);
                    return;
                }
                $m=$echanges->getMessages(new SQLFactory());
                if($m==false){
                    $return="[]";
                }else{
                if(gettype($m)=="array")
                $return=array_reduce($m,function($car,$el){
                    $car[]=$el->getArray();
                    return $car;
                },[]);
                else{
                    $return=[$m->getArray()];
                }
            }
                echo json_encode(["result"=>"ok","data"=>$return]);
                break;
            }
            case "getExchange":{
                break;
            }
            default:{
                echo json_encode(["error"=>0,"message"=>"no act parameters"]);
                break;
            }
        }
    }
}