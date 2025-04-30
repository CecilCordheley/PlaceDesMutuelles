<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class ReponseTbl{
    private $attr=["ID_MESSAGE"=>'',"ID_USER"=>'',"CONTENT_REPONSE"=>'',"DATE_REPONSE"=>'',"DISPLAY"=>''];
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
    public static function  add(SQLFactory $sqlF,ReponseTbl &$item){
     $return= $sqlF->addItem($item->getArray(),"reponse_tbl");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo $return;
      return false;
    } else {
      $item->ID_MESSAGE=$sqlF->lastInsertId("reponse_tbl");
      return false;
    }
    }
    public static function  update(SQLFactory $sqlF,ReponseTbl $item){
      $sqlF->updateItem($item->getArray(),"reponse_tbl");
    }
    public static function  del(SQLFactory $sqlF,ReponseTbl $item){
      $sqlF->deleteItem($item->ID_MESSAGE,"reponse_tbl");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM reponse_tbl");
      $return=[];
      foreach($query as $element){
      $entity=new ReponseTbl();
         $entity->ID_MESSAGE=$element["ID_MESSAGE"];
$entity->ID_USER=$element["ID_USER"];
$entity->CONTENT_REPONSE=$element["CONTENT_REPONSE"];
$entity->DATE_REPONSE=$element["DATE_REPONSE"];
$entity->DISPLAY=$element["DISPLAY"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getReponseTblBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM reponse_tbl WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new ReponseTbl();
         $entity->ID_MESSAGE=$element["ID_MESSAGE"];
$entity->ID_USER=$element["ID_USER"];
$entity->CONTENT_REPONSE=$element["CONTENT_REPONSE"];
$entity->DATE_REPONSE=$element["DATE_REPONSE"];
$entity->DISPLAY=$element["DISPLAY"];
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