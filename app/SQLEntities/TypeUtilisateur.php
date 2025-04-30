<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class TypeUtilisateur{
    private $attr=["idType_Utilisateur"=>'',"LIBELLE_Type_Utilisateur"=>''];
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
    public static function  add(SQLFactory $sqlF,TypeUtilisateur &$item){
     $return= $sqlF->addItem($item->getArray(),"type_utilisateur");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo $return;
      return false;
    } else {
      $item->idType_Utilisateur=$sqlF->lastInsertId("type_utilisateur");
      return false;
    }
    }
    public static function  update(SQLFactory $sqlF,TypeUtilisateur $item){
      $sqlF->updateItem($item->getArray(),"type_utilisateur");
    }
    public static function  del(SQLFactory $sqlF,TypeUtilisateur $item){
      $sqlF->deleteItem($item->idType_Utilisateur,"type_utilisateur");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM type_utilisateur");
      $return=[];
      foreach($query as $element){
      $entity=new TypeUtilisateur();
         $entity->idType_Utilisateur=$element["idType_Utilisateur"];
$entity->LIBELLE_Type_Utilisateur=$element["LIBELLE_Type_Utilisateur"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getTypeUtilisateurBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM type_utilisateur WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new TypeUtilisateur();
         $entity->idType_Utilisateur=$element["idType_Utilisateur"];
$entity->LIBELLE_Type_Utilisateur=$element["LIBELLE_Type_Utilisateur"];
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