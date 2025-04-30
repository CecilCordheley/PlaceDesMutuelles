<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class UtilisateurTbl{
    private $attr=["ID_USER"=>'',"DATE_VALIDITE"=>'',"PSEUDO_USER"=>'',"MAIL_USER"=>'',"MDP_USER"=>'',"ARRIVE_UTILISATEUR"=>'',"DATA_USER"=>'',"AVATAR_USER"=>'',"ID_ORGA"=>'',"TYPE_UTILISATEUR"=>'',"VALID_USER"=>''];
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
    public static function  add(SQLFactory $sqlF,UtilisateurTbl &$item){
      $item->DATA_USER=($item->DATA_USER=='')?"{}":$item->DATA_USER;
     $return= $sqlF->addItem($item->getArray(),"utilisateur_tbl");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo $return;
      return false;
    } else {
      $item->ID_USER=$sqlF->lastInsertId("utilisateur_tbl");
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,UtilisateurTbl $item){
      $sqlF->updateItem($item->getArray(),"utilisateur_tbl");
    }
    public static function  del(SQLFactory $sqlF,UtilisateurTbl $item){
      $sqlF->deleteItem($item->ID_USER,"utilisateur_tbl");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM utilisateur_tbl");
      $return=[];
      foreach($query as $element){
      $entity=new UtilisateurTbl();
         $entity->ID_USER=$element["ID_USER"];
$entity->PSEUDO_USER=$element["PSEUDO_USER"];
$entity->MAIL_USER=$element["MAIL_USER"];
$entity->MDP_USER=$element["MDP_USER"];
$entity->ARRIVE_UTILISATEUR=$element["ARRIVE_UTILISATEUR"];
$entity->DATE_VALIDITE=$element["DATE_VALIDITE"];
$entity->DATA_USER=$element["DATA_USER"]!=''?addslashes($element["DATA_USER"]):"";
$entity->AVATAR_USER=$element["AVATAR_USER"];
$entity->ID_ORGA=$element["ID_ORGA"];
$entity->TYPE_UTILISATEUR=$element["TYPE_UTILISATEUR"];
$entity->VALID_USER=$element["VALID_USER"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getUtilisateurTblBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM utilisateur_tbl WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new UtilisateurTbl();
         $entity->ID_USER=$element["ID_USER"];
$entity->PSEUDO_USER=$element["PSEUDO_USER"];
$entity->MAIL_USER=$element["MAIL_USER"];
$entity->MDP_USER=$element["MDP_USER"];
$entity->ARRIVE_UTILISATEUR=$element["ARRIVE_UTILISATEUR"];
$entity->DATE_VALIDITE=$element["DATE_VALIDITE"];
$entity->DATA_USER=$element["DATA_USER"]!=''?addslashes($element["DATA_USER"]):"";
$entity->AVATAR_USER=$element["AVATAR_USER"];
$entity->ID_ORGA=$element["ID_ORGA"];
$entity->TYPE_UTILISATEUR=$element["TYPE_UTILISATEUR"];
$entity->VALID_USER=$element["VALID_USER"];
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