<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\ExchangeTbl;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `Exchange`.
* Hérite de `ExchangeTbl`. Ajoutez ici vos propres méthodes.
*/
class ExchangeEntity extends ExchangeTbl
{
   // Ajoutez vos méthodes ici
   public function addMessage(SQLFactory $sqlF,UtilisateurEntity $user,string $contentMessage){
    //Vérifie si l'utilisateur est dans le bon organisme
    if($user->ID_ORGA!=$this->Organisme){
      throw new Exception("{$user->PSEUDO_USER} can't send message in this exchange");
    }
    $message=new IntMessageEntity;
    $message->contentIntMessage=$contentMessage;
    $message->utilisateur=$user->ID_USER;
    $message->Exchange=$this->idExchange;
    $message->dateIntMessage=date("Y-m-d H:i:s");
    return IntMessageEntity::add($sqlF,$message);
   }
  /**
   * Recupère les Messages d'un échange
   * @param mixed $sqlF
   * @return mixed
   */
  public function getMessages($sqlF){
    $arr=IntMessageEntity::getIntMessageTblBy($sqlF,"Exchange",$this->idExchange);
    return $arr;
  }
  public static function  add(SQLFactory $sqlF,ExchangeTbl &$item,$callBack=null){
    $newItem=$item->getArray();
    if(isset($newItem["UtilisateurEntity"])){
      unset($newItem["UtilisateurEntity"]);
    }
    $return= $sqlF->addItem($newItem,"exchange_tbl");
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
  public function getArray(){
    $arr=parent::getArray();
    $arr["UtilisateurEntity"]=UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(),"ID_USER",$this->Utilisateur)->getArray();
    return $arr;
  }
   public static function getAll($sqlF){
    $arr=ExchangeTbl::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(ExchangeTbl::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\ExchangeEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getExchangeTblBy($sqlF,$key,$value,$filter=null){
      $arr=ExchangeTbl::getExchangeTblBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\ExchangeEntity");
        return $c;
      },[]);
    }else return Main::fixObject($arr,"SQLEntities\ExchangeEntity");
    }else{
      return false;
    }
      }
 }