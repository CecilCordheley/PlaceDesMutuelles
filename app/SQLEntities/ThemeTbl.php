<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class ThemeTbl{
    private $attr=["ID_THEME"=>'',"LIB_THEME"=>''];
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
    public static function  add(SQLFactory $sqlF,ThemeTbl &$item){
     $return= $sqlF->addItem($item->getArray(),"theme_tbl");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo $return;
      return false;
    } else {
      $item->ID_THEME=$sqlF->lastInsertId("theme_tbl");
      return false;
    }
    }
    public static function  update(SQLFactory $sqlF,ThemeTbl $item){
      $sqlF->updateItem($item->getArray(),"theme_tbl");
    }
    public static function  del(SQLFactory $sqlF,ThemeTbl $item){
      $sqlF->deleteItem($item->ID_THEME,"theme_tbl");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM theme_tbl");
      $return=[];
      foreach($query as $element){
      $entity=new ThemeTbl();
         $entity->ID_THEME=$element["ID_THEME"];
$entity->LIB_THEME=$element["LIB_THEME"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getThemeTblBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM theme_tbl WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new ThemeTbl();
         $entity->ID_THEME=$element["ID_THEME"];
$entity->LIB_THEME=$element["LIB_THEME"];
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