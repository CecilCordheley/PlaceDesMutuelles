<?php
namespace Async;
use SQLEntities\ExchangeEntity;
use SQLEntities\StateuserTbl;
use SQLEntities\UtilisateurTblHasStateuserTbl;
use SQLEntities\UtilisateurEntity;
use SQLEntities\IntMessageEntity;
use Async\asyncFunction;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\SQLFactory;
// private $attr=["USER"=>'',"STATE"=>'',"DATE_STATE"=>'',"MOTIF_STATE"=>''];
class AsyncUser extends asyncFunction{
    public function handle($args,$act=null){
        parent::handle($args);
       
        switch($act){
            case "banUser":{
                date_default_timezone_set('Europe/Paris');
                if(!isset($args["id"])){
                    echo json_encode(["error"=>1,"message"=>"no user ID  parameters"]);
                    return;
                }
                //Vérifier si l'utilisateur a déjà un stat type banned
                $user=UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(),"ID_USER",$args["id"]);
                $sqlF=new SQLFactory();
                $Ustat=$user->getState($sqlF);
          //  EasyFrameWork::Debug(end($Ustat)->STATE);
                if(end($Ustat)->STATE===3){
                    echo json_encode(["error"=>2,"message"=>"user already under banned statu"]);
                    return;
                }
                $stat=new UtilisateurTblHasStateuserTbl();
                $stat->USER=$args["id"];
                $stat->STATE='3';
                $stat->DATE_STATE=date("Y-m-d h-i-s");
                $stat->MOTIF_STATE=$args["comment"];
                if(UtilisateurTblHasStateuserTbl::add($sqlF,$stat)!=false){
                    echo json_encode(["result"=>"ok"]);
                }else{
                    echo json_encode(["result"=>"ko"]);
                }
                break;
            }
            case "getUser":{
                if(!isset($args["id"])){
                    echo json_encode(["error"=>1,"message"=>"no user ID  parameters"]);
                    return;
                }
                $user=UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(),"ID_USER",$args["id"]);
                if($user==false){
                    echo json_encode(["error"=>2,"message"=>"no user found for id ".$args["id"]]);
                    return;
                }
                $return=$user->getArray();
                unset($return["MDP_USER"]);
                unset($return["DATA_USER"]);
                echo json_encode([
                    "result"=>"ok",
                    "data"=>$return
                ]);
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