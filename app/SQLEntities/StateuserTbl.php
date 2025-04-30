<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class StateuserTbl{
    private $attr=["ID_STATE"=>'',"LIBELLE_STATE"=>''];
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
    public static function  add(SQLFactory $sqlF,StateuserTbl &$item){
     $return= $sqlF->addItem($item->getArray(),"stateuser_tbl");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo $return;
      return false;
    } else {
      $item->ID_STATE=$sqlF->lastInsertId("stateuser_tbl");
      return false;
    }
    }
    public static function  update(SQLFactory $sqlF,StateuserTbl $item){
      $sqlF->updateItem($item->getArray(),"stateuser_tbl");
    }
    public static function  del(SQLFactory $sqlF,StateuserTbl $item){
      $sqlF->deleteItem($item->ID_STATE,"stateuser_tbl");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM stateuser_tbl");
      $return=[];
      foreach($query as $element){
      $entity=new StateuserTbl();
         $entity->ID_STATE=$element["ID_STATE"];
$entity->LIBELLE_STATE=$element["LIBELLE_STATE"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getStateuserTblBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM stateuser_tbl WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new StateuserTbl();
         $entity->ID_STATE=$element["ID_STATE"];
$entity->LIBELLE_STATE=$element["LIBELLE_STATE"];
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