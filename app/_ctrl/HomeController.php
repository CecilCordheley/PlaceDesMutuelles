<?php
namespace vendor\easyFrameWork\Core\Master\Controller;

use SQLEntities\MessageEntity;
use SQLEntities\UtilisateurEntity;
use vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\ResourceManager;
use vendor\easyFrameWork\Core\Master\EasyTemplate;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Main;
use SQLEntities\SujetEntity;
use SQLEntities\ThemeTbl;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Vendor\EasyFrameWork\Core\Master\SQLtoView;


    class HomeController extends Controller{
        public function setSujetByOrga(EasyTemplate &$template,$user){
            $SQL2V = new SQLtoView(new SQLFactory(),"./sqlView/index/messagebyOrga.view");
            $idOrga=$user->ID_ORGA!=null?"ID_ORGA={$user->ID_ORGA}":"1=1";
            $param=[];
            $param["query"] = "SELECT ID_THEME,ID_SUJET,URL_SUJET,LIB_SUJET,(SELECT '0') AS NBMESSAGE FROM sujet_tbl s INNER JOIN utilisateur_tbl u ON s.ID_USER=u.ID_USER WHERE $idOrga";
         //   EasyFrameWork::Debug($param["query"]);
            $param["callback"] = function (&$item, $previousItem, $defaultString) {
                $sqlF=new SQLFactory();
                $s=SujetEntity::getSujetTblBy($sqlF,"ID_SUJET",$item["ID_SUJET"]);
                if($s!=false){
                    $item["CLOSED"]=$s->DATE_CLOTURE?"1":"0";
                    $item["ID_THEME"]=strval($s->ID_THEME);
                    $item["THEME"]=$s->getTheme($sqlF)->LIB_THEME;
                    $item["ID_SUJET"]=strval($s->ID_SUJET);
                  $m=  $s->lastMessage($sqlF);
                  $messages=$s->getMessages($sqlF);
                  $nbMessage=0;
                  switch(gettype($messages)){
                    case "array":
                        $nbMessage=count($messages);
                        break;
                    case "object":
                        $nbMessage=1;
                        break;
                    default:
                    $nbMessage=0;
                    break;
                  }
                $lastMessage=($m!=false)?$m->CONTENT_MESSAGE:"";
                $item["NBMESSAGE"]="$nbMessage";
                    $item["LASTMESSAGE"] = $lastMessage;
                return $defaultString;
                }
            };
            $template->_view("messageByOrga", $SQL2V, $param);
        }
        public function __construct(){
            parent::__construct();
            $sessionManager=EasyGlobal::createSessionManager();
           $isConnect=(($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT))!=null)?"1":"0";
            $this->setData("isConnect",$isConnect);
        if($isConnect=="1"){
            $u=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
          //  EasyFrameWork::Debug($u->getState(new SQLFactory()));
            $user=$u->getArray();
            $user["NOM_ORGANISME"]=$user["ORGANISME"]!=false?$user["ORGANISME"]:"-";
            $this->setData("user",$user);
        }
                
        }
        
        public function handleRequest()
        {  
            $sessionManager=EasyGlobal::createSessionManager();
            $config=parse_ini_file("include/config.ini",true)["localhost"];
            $template = new EasyTemplate($config,new ResourceManager());
           // $template->addStylesheet("_css/index.css");
          //  $template->addScript("https://cdn.tailwindcss.com");
           
                $template->addStylesheet("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");
                $template->remplaceTemplate("MainContent","index.tpl");
                $template->addStylesheet("_css/index.css");
               // EasyFrameWork::Debug($param["streamers"]);
               $sqlF=new SQLFactory();
               $i=0;
               $u=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
               if($u!=false){
                     $this->setSujetByOrga($template,$u);
                     $events=$u->getOrganisme($sqlF)->getEvents($sqlF);
                 //    EasyFrameWork::Debug($events);
                     $template->setLoop("orgaEvent",array_reduce($events,function($c,$e){
                        $c[]=$e->getArray();
                        return $c;
                     },[]));
               }else{
                $template->clearView("messageByOrga");
               }
               
               $template->setLoop("catFilt",array_reduce(ThemeTbl::getAll($sqlF),function($c,$e){
                $c[]=$e->getArray();
                return $c;
               },[]));
               $template->addScript("_js/index.js");
              // EasyFrameWork::Debug(SujetEntity::getCommonSujet($sqlF));
               $sujet=array_reduce(SujetEntity::getCommonSujet($sqlF),function($c,$e) use (&$i,$sqlF,$sessionManager){
             //   EasyFrameWork::Debug($e);
             

             $u=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
             
             if($e->DISPLAY_SUJET=='1'){
                
              
                $c[$i]=$e->getArray();
                $c[$i]["CLOSED"]=$e->DATE_CLOTURE?"1":"0";
                $c[$i]["ID_SUJET"]=strval($e->ID_SUJET);
                $nbMess=$e->getMessages($sqlF);
                
                $last=$e->lastMessage($sqlF);
                if($last){
                    $dateMessage=Main::FormatDate($last->DATE_MESSAGE);
                    $c[$i]["LASTMESSAGE"]="<span>{$dateMessage}</span> {$last->CONTENT_MESSAGE}";
                }else{
                    $c[$i]["LASTMESSAGE"]="";
                }
               
                
                $c[$i]["THEME"]=$e->getTheme($sqlF)->LIB_THEME;
                switch(gettype($nbMess)){
                    case "array":
                        $c[$i]["NBMESSAGE"]=strval(count($nbMess));
                        break;
                    case "object":
                        $c[$i]["NBMESSAGE"]="1";
                        break;
                    default:
                        $c[$i]["NBMESSAGE"]="0";
                }
                $i++;
            }
                return $c;
               },[]);
           //    EasyFrameWork::Debug($sujet);
               $template->setLoop("topic",$sujet);
               $template->addStylesheet("_css/alert.css");
               $template->addStylesheet("_css/alert.css");
               $template->getRessourceManager()->addDirectJs("window.addEventListener('load',function(){
    document.querySelectorAll('.needConnect').forEach(el=>{
    el.onclick=function(){
        _alert('vous devez vous connecter pour accèder à cette section',function(){
            window.location.href=\"connexion\";
        });
        return false;
    }});
        
    })");
            $template->setVariables($this->getData());
            // Rendre le template
            $template->render();
            
        }
      
    }