<?php
namespace Async;

use SQLEntities\UtilisateurEntity;
use SQLEntities\MessageEntity;
use Async\asyncFunction;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\SQLFactory;
// private $attr=["USER"=>'',"STATE"=>'',"DATE_STATE"=>'',"MOTIF_STATE"=>''];
class AsyncMessage extends asyncFunction{
    private function toogleMessage(MessageEntity $message){
        $message->DISPLAY_=($message->DISPLAY_=="1")?"0":"1";
        MessageEntity::update(new SQLFactory(),$message);
        echo json_encode(["result"=>"ok","displayValue"=>$message->DISPLAY_]);
    }
    public function handle($args,$act=null){
        parent::handle($args);
       
        switch($act){
            case "toogleMessage":{
                if(!isset($args["id"])){
                    echo json_encode(["error"=>1,"message"=>"no message ID  parameters"]);
                    return;
                }
                $m=MessageEntity::getMessageTblBy(new SQLFactory(),"ID_MESSAGE",$args["id"]);
                if($m==false){
                    echo json_encode(["error"=>2,"message"=>"no message with ID  parameters"]);
                    return;
                }
                $this->toogleMessage($m);
                break;
            }
        }
    }
}