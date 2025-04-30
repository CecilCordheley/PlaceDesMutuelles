<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class EventTbl{
    private $attr=["idEvent"=>'',"titreEvent"=>'',"descEvent"=>'',"dateEvent"=>'',"ID_ORGA"=>''];
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
    public static function  add(SQLFactory $sqlF,EventTbl &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"event_tbl");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->idEvent=$sqlF->lastInsertId("event_tbl");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,EventTbl $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"event_tbl");
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
    public static function  del(SQLFactory $sqlF,EventTbl $item){
      $sqlF->deleteItem($item->idEvent,"event_tbl");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM event_tbl");
      $return=[];
      foreach($query as $element){
      $entity=new EventTbl();
         $entity->idEvent=$element["idEvent"];
$entity->titreEvent=$element["titreEvent"];
$entity->descEvent=$element["descEvent"];
$entity->dateEvent=$element["dateEvent"];
$entity->ID_ORGA=$element["ID_ORGA"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getEventTblBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM event_tbl WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new EventTbl();
         $entity->idEvent=$element["idEvent"];
$entity->titreEvent=$element["titreEvent"];
$entity->descEvent=$element["descEvent"];
$entity->dateEvent=$element["dateEvent"];
$entity->ID_ORGA=$element["ID_ORGA"];
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