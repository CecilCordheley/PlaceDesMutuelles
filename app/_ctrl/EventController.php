<?php
namespace vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\ResourceManager;
use vendor\easyFrameWork\Core\Master\EasyTemplate;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use SQLEntities\EventEntity;
use SQLEntities\UtilisateurEntity;
use vendor\easyFrameWork\Core\Master\SQLFactory;
class EventController extends Controller{
    public function __construct(){
        parent::__construct();
        $sessionManager=EasyGlobal::createSessionManager();
       $isConnect=(($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT))!=null)?"1":"0";
        $this->setData("isConnect",$isConnect);
        if($isConnect=="1"){
            $user=$sessionManager->get("user",SessionManager::PUBLIC_CONTEXT);
            $userA=(Main::fixObject($user,"SQLEntities\UtilisateurEntity"))->getArray();
            $this->setData("user",$userA);
        }        
    }
    public function handleRequest(){
        $sessionManager=EasyGlobal::createSessionManager();
        
      //  EasyFrameWork::Debug($user->TYPE_UTILISATEUR);
        $config=parse_ini_file("include/config.ini",true)["localhost"];
        $template = new EasyTemplate($config,new ResourceManager());
        $template->addStylesheet("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");
        $template->addStylesheet("_css/event.css");
        $template->remplaceTemplate("MainContent","EventList.tpl");
        //Listes Evenements
        $events=EventEntity::getLastEvents(new SQLFactory());
   //     EasyFrameWork::Debug($events);
   $user=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
        $template->setLoop("EventList",array_reduce($events,function($c,$e) use($user){
            if($user->ID_ORGA==$e->ID_ORGA){
            $c[]=$e->getArray();
            }
            return $c;
        },[]));
        
        if($user->TYPE_UTILISATEUR!="1"){
            $template->getRessourceManager()->addDirectJs("$(function(){\n 
           $('#addEventTrigger').click(function(){
           console.log('effectue les vérifications du formulaire');
           $('#addEventForm').submit();
           });
            });");
        }
        if(isset($_GET["act"])){
            switch($_GET["act"]){
                case "delEvent":{
                    $sqlF=new SQLFactory();
                    $event=EventEntity::getEventTblBy($sqlF,"idEvent",$_GET["id"]);
                    EventEntity::del($sqlF,$event);
                    $template->getRessourceManager()->addDirectJs("$(function(){\n 
                    alert('L\'évènement a été supprimé');
                    window.location.href='EventTest';
                 });");
                    break;
                }
                case "addEvent":{
                    $newEvent=new EventEntity;
                    $newEvent->titreEvent=$_POST["titleEvent"];
                    $newEvent->descEvent=$_POST["descEvent"];
                    $newEvent->dateEvent=$_POST["dateEvent"];
                    $newEvent->ID_ORGA=$user->ID_ORGA;
                    EventEntity::add(new SQLFactory(),$newEvent,function($event) use($template){
                        $template->getRessourceManager()->addDirectJs("$(function(){\n 
                        alert('L\'évènement a été ajouté');
                        window.location.href='EventTest';
                     });");
                    });
                        
                    
                    break;
                  //  EasyFrameWork::Debug($_POST);
                }
            }
        }

        $template->setVariables($this->getData());
        // Rendre le template
        $template->render();
    }
}