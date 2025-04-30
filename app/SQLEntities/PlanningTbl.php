<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class PlanningTbl{
    private $attr=["idPlanning"=>'',"jouer_semaine"=>'',"heure_debut"=>'',"heure_fin"=>'',"organisme_tbl_ID_ORGA"=>''];
    public function __set($name,$value){
      if (array_key_exists($name, $this->attr)) {
         $this->attr[$name]=$value;
     } else {
         throw new Exception("Propriété non définie : $name");
     }
    }
    public function getArray(){
      return $this->attr;
    }
    public function __get($name){
      if (array_key_exists($name, $this->attr)) {
         return $this->attr[$name];
     } else {
         throw new Exception("Propriété non définie : $name");
     }
    }
    public static function  add(SQLFactory $sqlF,PlanningTbl &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"planning_tbl");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->idPlanning=$sqlF->lastInsertId("planning_tbl");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,PlanningTbl $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"planning_tbl");
      if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
        echo "<pre>$return</pre>";
        return false;
      } else {
        if($callBack!=null){
          call_user_func($callBack,$item);
        }
        return true;
      }
    }
    public static function  del(SQLFactory $sqlF,PlanningTbl $item){
      $sqlF->deleteItem($item->idPlanning,"planning_tbl");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM planning_tbl");
      $return=[];
      foreach($query as $element){
      $entity=new PlanningTbl();
         $entity->idPlanning=$element["idPlanning"];
$entity->jouer_semaine=$element["jouer_semaine"];
$entity->heure_debut=$element["heure_debut"];
$entity->heure_fin=$element["heure_fin"];
$entity->organisme_tbl_ID_ORGA=$element["organisme_tbl_ID_ORGA"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getPlanningTblBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM planning_tbl WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new PlanningTbl();
         $entity->idPlanning=$element["idPlanning"];
$entity->jouer_semaine=$element["jouer_semaine"];
$entity->heure_debut=$element["heure_debut"];
$entity->heure_fin=$element["heure_fin"];
$entity->organisme_tbl_ID_ORGA=$element["organisme_tbl_ID_ORGA"];
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