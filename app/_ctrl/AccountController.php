<?php
namespace vendor\easyFrameWork\Core\Master\Controller;

use SQLEntities\OrganismeTbl;
use SQLEntities\QuestionTbl;
use vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\ResourceManager;
use vendor\easyFrameWork\Core\Master\EasyTemplate;
use vendor\easyFrameWork\Core\Master\SessionManager;
use SQLEntities\SujetEntity;
use SQLEntities\UtilisateurEntity;
use SQLEntities\UtilisateurTbl;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\SQLFactory;
    class AccountController extends Controller{
        private $data;
        public function __construct(){
            parent::__construct();
            $sessionManager=EasyGlobal::createSessionManager();
           $isConnect=(($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT))!=null)?"1":"0";
            $this->setData("isConnect",$isConnect);
            if($isConnect=="1"){
                $user=$sessionManager->get("user",SessionManager::PUBLIC_CONTEXT);
                $userA=(Main::fixObject($user,"SQLEntities\UtilisateurEntity"))->getArray();
                $dataUSer=(Main::fixObject($user,"SQLEntities\UtilisateurEntity"))->getData();
                $this->data=$dataUSer;
                $this->setData("user",$userA);
            }        
        }
        public function handleRequest(){
            $sessionManager=EasyGlobal::createSessionManager();
            $config=parse_ini_file("include/config.ini",true)["localhost"];
            $template = new EasyTemplate($config,new ResourceManager());
            switch($_GET["root"]){
                case "add":{
                   // EasyFrameWork::Debug($_POST);
                    $arrive=date("Y-m-d");
                    $valid=date("Y")."-".(((date("m")*1)+6)%12)."-".date("d");
                    $crypto=EasyFrameWork::getClassInstance("Cryptographer"); 
                    $mdp= $crypto->hashString($_POST["MDP_USER"]);
                    //Création de l'UTILISATEUR
                    $user=new UtilisateurTbl;
                    $user->PSEUDO_USER=$_POST["PSEUDO_USER"];
                    $user->MAIL_USER=$_POST["MAIL_USER"];
                    $user->MDP_USER=$mdp;
                    $user->ARRIVE_UTILISATEUR=$arrive;
                    $user->DATE_VALIDITE=$valid;
                    $user->VALID_USER=0;
                    $user->DATA_USER="{}";
                    $user->ID_ORGA=$_POST["ID_ORGA"];
                    $user->TYPE_UTILISATEUR=1;
                    $user->AVATAR_USER="default.png";
                    $add=UtilisateurTbl::add(new SQLFactory(),$user);
                    //Redirection vers l'acceuil
                    if($add)
                        Main::redirectWithAlert($template,"Bienvenue sur le Forum {$_POST["PSEUDO_USER"]}","index.php");
                    break;
                }
                case "see":{
                    $template->remplaceTemplate("MainContent","account.tpl");
                    $template->setLoop("DATA_USER",$this->data);
                    break;
                }
                case "new":{
                    $template->remplaceTemplate("MainContent","newAccount.tpl");
                    $orga=array_reduce(OrganismeTbl::getAll(new SQLFactory()),function($c,$e){
                        $c[]=$e->getArray();
                        return $c;
                    });
                    $template->setLoop("ORGA",$orga);
                    break;
                }
            }
                

            $template->setVariables($this->getData());
            // Rendre le template
            $template->render();
        }
    }