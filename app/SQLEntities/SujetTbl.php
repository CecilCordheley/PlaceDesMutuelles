<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class SujetTbl{
    private $attr=["DISPLAY_SUJET"=>'',"ID_SUJET"=>'',"LIB_SUJET"=>'',"URL_SUJET"=>'',"DATE_SUJET"=>'',"DATE_CLOTURE"=>'',"ID_USER"=>'',"ID_THEME"=>''];
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
    public static function  add(SQLFactory $sqlF,SujetTbl &$item){
     $return= $sqlF->addItem($item->getArray(),"sujet_tbl");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo $return;
      return false;
    } else {
      $item->ID_SUJET=$sqlF->lastInsertId("sujet_tbl");
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,SujetTbl $item){
      $sqlF->updateItem($item->getArray(),"sujet_tbl");
    }
    public static function  del(SQLFactory $sqlF,SujetTbl $item){
      $sqlF->deleteItem($item->ID_SUJET,"sujet_tbl");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM sujet_tbl");
      $return=[];
      foreach($query as $element){
      $entity=new SujetTbl();
         $entity->ID_SUJET=$element["ID_SUJET"];
$entity->LIB_SUJET=$element["LIB_SUJET"];
$entity->URL_SUJET=$element["URL_SUJET"];
$entity->DATE_SUJET=$element["DATE_SUJET"];
$entity->DATE_CLOTURE=$element["DATE_CLOTURE"];
$entity->ID_USER=$element["ID_USER"];
$entity->ID_THEME=$element["ID_THEME"];
$entity->DISPLAY_SUJET=$element["DISPLAY_SUJET"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getSujetTblBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM sujet_tbl WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new SujetTbl();
         $entity->ID_SUJET=$element["ID_SUJET"];
$entity->LIB_SUJET=$element["LIB_SUJET"];
$entity->URL_SUJET=$element["URL_SUJET"];
$entity->DATE_SUJET=$element["DATE_SUJET"];
$entity->DATE_CLOTURE=$element["DATE_CLOTURE"];
$entity->ID_USER=$element["ID_USER"];
$entity->ID_THEME=$element["ID_THEME"];
$entity->DISPLAY_SUJET=$element["DISPLAY_SUJET"];
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