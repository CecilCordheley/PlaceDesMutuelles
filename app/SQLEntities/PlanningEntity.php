<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\planningTbl;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `Planning`.
* Hérite de `planningTbl`. Ajoutez ici vos propres méthodes.
*/
class PlanningEntity extends planningTbl
{
   // Ajoutez vos méthodes ici

   public static function generateAddFrm($action){
    $frm="<form action='$action' method='POST'>";
    return $frm;
   }
   public static function getAll($sqlF){
    $arr=ExchangeTbl::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(planningTbl::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\PlanningEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getplanningTblBy($sqlF,$key,$value,$filter=null){
      $arr=planningTbl::getplanningTblBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\PlanningEntity");
        return $c;
      },[]);
    }else return $arr;
    }else{
      return false;
    }
      }
 }