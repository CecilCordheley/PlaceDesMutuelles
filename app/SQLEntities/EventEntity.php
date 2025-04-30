<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\EventTbl;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `Event`.
* Hérite de `EventTbl`. Ajoutez ici vos propres méthodes.
*/
class EventEntity extends EventTbl
{
   // Ajoutez vos méthodes ici

   public static function getAll($sqlF){
    $arr=ExchangeTbl::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(EventTbl::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\EventEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
  public static function getLastEvents($sqlF){
    $arr=self::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(EventTbl::getAll($sqlF),function($c,$e){
      $now=date("Y-m-d");
      if(Main::DateCompare($e->dateEvent,$now)!=0)
        $c[]=Main::fixObject($e,"SQLEntities\EventEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getEventTblBy($sqlF,$key,$value,$filter=null){
      $arr=EventTbl::getEventTblBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\EventEntity");
        return $c;
      },[]);
    }else return $arr;
    }else{
      return false;
    }
      }
 }