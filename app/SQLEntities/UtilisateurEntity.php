<?php
namespace SQLEntities;

use ArrayObject;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\SujetTbl;
use SQLEntities\MessageTbl;
use SQLEntities\ThemeTbl;
use vendor\easyFrameWork\Core\Main;
use Exception;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\SqlEntities;

class UtilisateurEntity extends UtilisateurTbl{
  public function getRights($index=null){
    if($this->TYPE_UTILISATEUR==1){
      throw new Exception("Cet utilisateur n'a pas de droit de gestion", 1);
    }
    if($index!=null){
      $rights=$this->getData("droit");
      if(key_exists($index,$rights))
        return str_split($rights[$index]);
      else
        return false;
    }
    else
     return $this->getData("droit");
  //  return $this->DATA_USER["droit"];
  }
  /**
   * Summary of getOrganisme
   * @param mixed $sqlF
   * @return OrganismeEntity
   */
  public function getOrganisme($sqlF){
    return OrganismeEntity::getOrganismeTblBy($sqlF,"ID_ORGA",$this->ID_ORGA);
  }
  /**
   * Summary of getExchange
   * @param SQLFactory $sqlF
   * @return mixed
   */
  public function getExchange(SQLFactory $sqlF):mixed{
    return ExchangeEntity::getExchangeTblBy($sqlF,"Utilisateur",$this->ID_USER);
  }
    public static function connexion($sqlF,$mail,$mdp){
        $r = self::getUtilisateurTblBy($sqlF,"MAIL_USER",$mail,function($el) use ($mdp){
         // EasyFrameWork::Debug($el);
          if($el->MDP_USER!="")
            return $el->MDP_USER===$mdp;
          else  
            return -1;
        });
       // EasyFrameWork::Debug($r);
        return $r;
    }
    /**
     * Retourne les sujets ouverts par l'utilisateur
     * @param SQLFactory $sqlF
     * @return mixed
     */
    public function getSujets($sqlF){
      return SujetEntity::getSujetTblBy($sqlF,"ID_USER",$this->ID_USER);
    }
    public function getArray():array{
      $return =parent::getArray();
      $orga=$this->getOrganisme(new SQLFactory());
      
      $return["ORGANISME"]=$orga!=false?$orga->NOM_ORGANISATION:false;
      $return["ROLE"]=$this->getRole(new SQLFactory())->LIBELLE_Type_Utilisateur;
      return $return;
    }
    /**
     * Retournes les informations complémentaire de l'utilisateur
     * @param string $key
     * @param bool $associativ
     * @return mixed
     */
    public function getData($key=null,$associativ=true){
      $datas= json_decode(stripslashes($this->DATA_USER),true);
    //  var_dump($datas);
      if($key==null){
        if($associativ){
          $return=[];
          $i=0;
          foreach($datas as $key=>$value){
            $return[$i]=[
              "KEY"=>$key,
              "VALUE"=>$value
            ];
            $i++;
          }
          return $return;
      }else
        return $datas;
      }
      else{
        if(key_exists($key,$datas))
          return $datas[$key];
        else
          false;
      }
    }
    /**
     * Retourne le Rôle de l'utilisateur
     * @param SQLFactory $sqlF
     * @return mixed
     */
    public function getRole($sqlF){
      return TypeUtilisateur::getTypeUtilisateurBy($sqlF,"idType_Utilisateur",$this->TYPE_UTILISATEUR);
    }
    /**
     * Retourne le statut de l'utilisateur (Banni, TimeOut, Allowed)
     * @param SQLFactory $sqlF
     * @return mixed
     */
    public function getState($sqlF){
      
      $stat=UtilisateurTblHasStateuserTbl::getUtilisateurTblHasStateuserTblBy($sqlF,"USER",$this->ID_USER);
      //echo gettype($stat);
      if(gettype($stat)=="array")
      usort($stat,function($a,$b){
        return Main::DateCompare($a->DATE_STATE,$b->DATE_STATE);
     });
      return $stat;
    }
    public static function getAll($sqlF){
      return array_reduce(UtilisateurTbl::getAll($sqlF),function($c,$e) use($sqlF){
        $u=Main::fixObject($e,"SQLEntities\UtilisateurEntity");
        $c[]=$u;
        return $c;
      },[]);
      }
      public static function getUtilisateurTblBy($sqlF,$key,$value,$filter=null){
        $return=UtilisateurTbl::getUtilisateurTblBy($sqlF,$key,$value,$filter);
        if(gettype($return)=="array")
        return array_reduce(UtilisateurTbl::getUtilisateurTblBy($sqlF,$key,$value,$filter),function($c,$e){
          $c[]=Main::fixObject($e,"SQLEntities\UtilisateurEntity");
          return $c;
        },[]);
        elseif(gettype($return)=="object"){
          return Main::fixObject($return,"SQLEntities\UtilisateurEntity");
        }else
          return false;
      }
}