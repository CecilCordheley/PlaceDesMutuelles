<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use vendor\easyFrameWork\Core\Main;
use SQLEntities\SujetTbl;
use SQLEntities\MessageTbl;
use SQLEntities\ThemeTbl;
use SQLEntities\UtilisateurEntity;
use Exception;

class SujetEntity extends SujetTbl{
    public function getMessages($sqlF){
        return MessageTbl::getMessageTblBy($sqlF,"ID_SUJET",$this->ID_SUJET);
    }
    public function getTheme($sqlF){
        return ThemeTbl::getThemeTblBy($sqlF,"ID_THEME",$this->ID_THEME);
    }
    public function getUtilisateur($sqlF){
        return UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(),"ID_USER",$this->ID_USER);
    }
    public function lastMessage($sqlF){
        $m=$this->getMessages($sqlF);
        if(gettype($m)=="array"){
        usort($m,function($a,$b){
            $date1=explode(strtotime($a->DATE_MESSAGE),' ')[0];
            $date2=explode(strtotime($b->DATE_MESSAGE),' ')[0];
            if($date1==$date2){
                return 0;
            }
            return ($date1 < $date2) ? -1 : 1;
        });
        return $m[0];
    }else{
        return $m;
    }
    }
    public function getUser($sqlF){
        return UtilisateurEntity::getUtilisateurTblBy($sqlF,"ID_USER",$this->ID_USER);
    }
    public static function getAll($sqlF){
        return array_reduce(SujetTbl::getAll($sqlF),function($c,$e){
            $c[]=Main::fixObject($e,"SQLEntities\SujetEntity");
            return $c;
          },[]);
      }
      public static function getCommonSujet(SQLFactory $sqlF){
        $arr=self::getAll($sqlF);
        return array_filter($arr,function($el){
            return $el->ID_USER==null;
        });
      }
      /**
       * Summary of getSujetTblBy
       * @param mixed $sqlF
       * @param mixed $key
       * @param mixed $value
       * @param mixed $filter
       * @return mixed
       */
      public static function getSujetTblBy($sqlF,$key,$value,$filter=null){
        $item=SujetTbl::getSujetTblBy($sqlF,$key,$value,$filter);
    //    echo gettype($item)=="array";
        if(gettype($item)=="array")
       return array_reduce($item,function($c,$e) use($sqlF){
        $u=Main::fixObject($e,"SQLEntities\SujetEntity");
        $c[]=$u;
        return $c;
      },[]);
      else
        return Main::fixObject($item,"SQLEntities\SujetEntity");
      }
}