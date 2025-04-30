<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class OrganismeTbl{
    private $attr=["ID_ORGA"=>'',"NOM_ORGANISATION"=>'',"DATE_ORGANISME"=>'',"INFO_MUTUELLE"=>'',"LOGO_ORGANISME"=>'',"ALLOWED_ORGA"=>''];
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
    public static function  add(SQLFactory $sqlF,OrganismeTbl &$item){
     $return= $sqlF->addItem($item->getArray(),"organisme_tbl");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo $return;
      return false;
    } else {
      $item->ID_ORGA=$sqlF->lastInsertId("organisme_tbl");
      return false;
    }
    }
    public static function  update(SQLFactory $sqlF,OrganismeTbl $item){
      $sqlF->updateItem($item->getArray(),"organisme_tbl");
    }
    public static function  del(SQLFactory $sqlF,OrganismeTbl $item){
      $sqlF->deleteItem($item->ID_ORGA,"organisme_tbl");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM organisme_tbl");
      $return=[];
      foreach($query as $element){
      $entity=new OrganismeTbl();
         $entity->ID_ORGA=$element["ID_ORGA"];
$entity->NOM_ORGANISATION=$element["NOM_ORGANISATION"];
$entity->DATE_ORGANISME=$element["DATE_ORGANISME"];
$entity->INFO_MUTUELLE=$element["INFO_MUTUELLE"];
$entity->LOGO_ORGANISME=$element["LOGO_ORGANISME"];
$entity->ALLOWED_ORGA=$element["ALLOWED_ORGA"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getOrganismeTblBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM organisme_tbl WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new OrganismeTbl();
         $entity->ID_ORGA=$element["ID_ORGA"];
$entity->NOM_ORGANISATION=$element["NOM_ORGANISATION"];
$entity->DATE_ORGANISME=$element["DATE_ORGANISME"];
$entity->INFO_MUTUELLE=$element["INFO_MUTUELLE"];
$entity->LOGO_ORGANISME=$element["LOGO_ORGANISME"];
$entity->ALLOWED_ORGA=$element["ALLOWED_ORGA"];
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