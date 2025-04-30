<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\MessageTbl;
use SQLEntities\ReponseTbl;
use vendor\easyFrameWork\Core\Main;
use Exception;
class MessageEntity extends MessageTbl{
    public function getReponse($sqlF){
        return ReponseTbl::getReponseTblBy($sqlF,"ID_MESSAGE",$this->ID_MESSAGE);
    }
    public function getUSer($sqlF){
      return UtilisateurEntity::getUtilisateurTblBy($sqlF,"ID_USER",$this->ID_USER);
    }
    public function getSujet($sqlF){
      return SujetEntity::getSujetTblBy($sqlF,"ID_SUJET",$this->ID_SUJET);
    }
    public static function getAll($sqlF){
      return array_reduce(MessageTbl::getAll($sqlF),function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\MessageEntity");
        return $c;
      },[]);
      }
      public static function getMessageTblBy($sqlF,$key,$value,$filter=null){
        $query=$sqlF->prepareQuery("SELECT * FROM message_tbl WHERE $key=:val",$key,$value);
        $return=[];
        foreach($query as $element){
        $entity=new MessageEntity();
           $entity->ID_MESSAGE=$element["ID_MESSAGE"];
  $entity->DATE_MESSAGE=$element["DATE_MESSAGE"];
  $entity->CONTENT_MESSAGE=$element["CONTENT_MESSAGE"];
  $entity->DISPLAY_=$element["DISPLAY_"];
  $entity->ID_USER=$element["ID_USER"];
  $entity->ID_SUJET=$element["ID_SUJET"];
        $return[]=$entity;
        }
        if($filter!=null && count($return)>0){
          $return = array_filter($return,$filter);
        }
        if(count($return))
        return (count($return) > 1) ? $return : $return[0];
      else
        return false;
      }
}