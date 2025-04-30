<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\IntMessageTbl;
use SQLEntities\UtilisateurEntity;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `IntMessage`.
* Hérite de `IntMessageTbl`. Ajoutez ici vos propres méthodes.
*/
class IntMessageEntity extends IntMessageTbl
{
   // Ajoutez vos méthodes ici
   public function getArray(){
    $arr=parent::getArray();
   // $arr["utilisateur"]=UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(),"ID_USER",$this->utilisateur)->getArray();
    return $arr;
  }
   public static function getAll($sqlF){
    $arr=ExchangeTbl::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(IntMessageTbl::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\IntMessageEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getIntMessageTblBy($sqlF,$key,$value,$filter=null){
      $arr=IntMessageTbl::getIntMessageTblBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\IntMessageEntity");
        return $c;
      },[]);
    }else return Main::fixObject($arr,"SQLEntities\IntMessageEntity");
    }else{
      return false;
    }
      }
 }