<?php

namespace vendor\easyFrameWork\Core\Master\Controller;

use SQLEntities\QuestionTbl;
use vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\ResourceManager;
use vendor\easyFrameWork\Core\Master\EasyTemplate;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use SQLEntities\SujetEntity;
use SQLEntities\UtilisateurEntity;
use SQLEntities\UtilisateurTbl;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use DateTime;
class RootController extends Controller{
    public function __construct(){
        parent::__construct();    
    }
    public function updatePassWord($template){
        if($_POST["user_mdp"]!=$_POST["user_check"]){
            Main::redirectWithAlert($template,"Les mots de passe saisis sont incorect","root.php?root=updatePassWord");
        }
        $crypto=EasyFrameWork::getClassInstance("Cryptographer"); 
        $mdp= $crypto->hashString($_POST["user_mdp"]);
        $user=UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(),"MAIL_USER",$_POST["user_mail"]);
        if($user){
        $user->MDP_USER=$mdp;
        $arrive = date("Y-m-d");
        $date = new DateTime($arrive);
        // Ajout de 6 mois
$date->modify('+6 months');

// Récupération de la date modifiée
$valid = $date->format('Y-m-d');

// Ajout de 6 mois
$date->modify('+6 months');

// Récupération de la date modifiée
$valid = $date->format('Y-m-d');
        $user->DATE_VALIDITE=$valid;
        UtilisateurEntity::update(new SQLFactory(),Main::fixObject($user,"SQLEntities\UtilisateurTbl"));
        $sessionManager=EasyGlobal::createSessionManager();
        $sessionManager->set("user",$user,SessionManager::PUBLIC_CONTEXT);
        Main::redirectWithAlert($template,"Bienvenue sur le Forum {$user->PSEUDO_USER}","index.php");
        }else{
            Main::redirectWithAlert($template,"Le mail saisi est invalide","root.php?root=updatePassWord");
        }
    }
    public function connexion($template){
        $crypto=EasyFrameWork::getClassInstance("Cryptographer"); 
        $mdp= $crypto->hashString($_POST["user_mdp"]);
        $user=UtilisateurEntity::connexion(new SQLFactory(),$_POST["user_mail"],$mdp);
     // EasyFrameWork::Debug($user);
        if($user){
            if($user->VALID_USER){
            $sessionManager=EasyGlobal::createSessionManager();
            $sessionManager->set("user",$user,SessionManager::PUBLIC_CONTEXT);
            Main::redirectWithAlert($template,"Bienvenue sur le Forum {$user->PSEUDO_USER}","index.php");
            }else{
                Main::redirectWithAlert($template,"Vous devez mettre à jour votre mot de passe","root.php?root=updatePassWord");
            }
        }else{
            Main::redirectWithAlert($template,"La connexion a échouée, mot de passe ou mail erronné","connexion");
        }
    }
    public function handleRequest(){
       
        $sessionManager=EasyGlobal::createSessionManager();
            $config=parse_ini_file("include/config.ini",true)["localhost"];
            $template = new EasyTemplate($config,new ResourceManager());
            $template->addStylesheet("_css/form.css");
            $template->addStylesheet("_css/alert.css");
            $template->addScript("_js/alert.js");
            $template->addStylesheet("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");
            if(isset($_GET["root"])){
            switch($_GET["root"]){
                case "updatePassWord":{
                    $template->remplaceTemplate("MainContent","updatePassword.tpl");
                    break;
                }
                case "connexion":{
                    $template->remplaceTemplate("MainContent","connexion.tpl");
                    break;
                }
                case "deconnexion":{
                    if($sessionManager->sessionExist())
                        $sessionManager->clean();
                    Main::redirectWithAlert($template,"Vous avez été déconnecté <br>Au revoir","index.php");
                    break;
                }
            }
        }
        if(isset($_GET['act'])){
            if($_GET["act"]=="connexion"){
                $this->connexion($template);
            }elseif($_GET["act"]=="udpatePassword"){
                $this->updatePassWord($template);
            }
        }
            $template->setVariables($this->getData());
            // Rendre le template
            $template->render();
            
    }
}