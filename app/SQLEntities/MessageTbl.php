<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class MessageTbl{
    private $attr=["ID_MESSAGE"=>'',"DATE_MESSAGE"=>'',"CONTENT_MESSAGE"=>'',"DISPLAY_"=>'',"ID_USER"=>'',"ID_SUJET"=>''];
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
    public static function  add(SQLFactory $sqlF,MessageTbl &$item){
     $return= $sqlF->addItem($item->getArray(),"message_tbl");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo $return;
      return false;
    } else {
      $item->ID_MESSAGE=$sqlF->lastInsertId("message_tbl");
      return false;
    }
    }
    public static function  update(SQLFactory $sqlF,MessageTbl $item){
      $sqlF->updateItem($item->getArray(),"message_tbl");
    }
    public static function  del(SQLFactory $sqlF,MessageTbl $item){
      $sqlF->deleteItem($item->ID_MESSAGE,"message_tbl");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM message_tbl");
      $return=[];
      foreach($query as $element){
      $entity=new MessageTbl();
         $entity->ID_MESSAGE=$element["ID_MESSAGE"];
$entity->DATE_MESSAGE=$element["DATE_MESSAGE"];
$entity->CONTENT_MESSAGE=$element["CONTENT_MESSAGE"];
$entity->DISPLAY_=$element["DISPLAY_"];
$entity->ID_USER=$element["ID_USER"];
$entity->ID_SUJET=$element["ID_SUJET"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getMessageTblBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM message_tbl WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new MessageTbl();
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