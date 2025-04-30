<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class ExchangeTbl{
    private $attr=["idExchange"=>'',"uuidExchange"=>'',"dateExchange"=>'','HeureDebut'=>"","Utilisateur"=>'',"Organisme"=>'','StateExchange'=>''];
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
    public static function  add(SQLFactory $sqlF,ExchangeTbl &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"exchange_tbl");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->idExchange=$sqlF->lastInsertId("exchange_tbl");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,ExchangeTbl $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"exchange_tbl");
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
    public static function  del(SQLFactory $sqlF,ExchangeTbl $item){
      $sqlF->deleteItem($item->idExchange,"exchange_tbl");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM exchange_tbl");
      $return=[];
      foreach($query as $element){
      $entity=new ExchangeTbl();
         $entity->idExchange=$element["idExchange"];
$entity->uuidExchange=$element["uuidExchange"];
$entity->dateExchange=$element["dateExchange"];
$entity->HeureDebut=$element["HeureDebut"];
$entity->Utilisateur=$element["Utilisateur"];
$entity->Organisme=$element["Organisme"];
$entity->StateExchange=$element["StateExchange"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getExchangeTblBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM exchange_tbl WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new ExchangeTbl();
         $entity->idExchange=$element["idExchange"];
         $entity->uuidExchange=$element["uuidExchange"];
         $entity->dateExchange=$element["dateExchange"];
         $entity->HeureDebut=$element["HeureDebut"];
         $entity->Utilisateur=$element["Utilisateur"];
         $entity->Organisme=$element["Organisme"];
         $entity->StateExchange=$element["StateExchange"];
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