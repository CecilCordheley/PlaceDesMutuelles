<?php
namespace vendor\easyFrameWork\Core\Master\Controller;


use SQLEntities\SujetTbl;
use vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\ResourceManager;
use vendor\easyFrameWork\Core\Master\EasyTemplate;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Master\SQLtoView;
use Vendor\EasyFrameWork\Core\Master\SqlEntities;
use SQLEntities\SujetEntity;
use SQLEntities\UtilisateurEntity;
use SQLEntities\MessageEntity;
use SQLEntities\ThemeTbl;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use vendor\easyFrameWork\Core\Main;
    class SujetController extends Controller{
        public function __construct(){
            parent::__construct();
            $config=parse_ini_file("include/config.ini",true)["localhost"];
            $template = new EasyTemplate($config,new ResourceManager());
            $sessionManager=EasyGlobal::createSessionManager();
           $isConnect=(($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT))!=null)?"1":"0";
            $this->setData("isConnect",$isConnect);
         //   echo "<pre>$isConnect</pre>";
            if($isConnect==="1"){
                $u=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
                if($u){
                $user=$u->getArray();
              //  EasyFrameWork::Debug($u->getRole(new SQLFactory()));
                $user["NOM_ORGANISME"]=$u->getOrganisme(new SQLFactory())->NOM_ORGANISATION;
                $this->setData("user",$user);
                if(isset($_GET["act"])){
                    switch($_GET["act"]){
                        case "POST":{
                           
                            $sujet=SujetEntity::getSujetTblBy(new SQLFactory(),"ID_SUJET",$_GET["id"]);
                            $url = "sujet-{$_GET['id']}-{$sujet->URL_SUJET}.html";
                            $m = new MessageEntity();
                            $m->ID_USER=$u->ID_USER;
                            $m->ID_SUJET=$_GET["id"];
                            $message = $_POST["message"];
                            $message = htmlentities($message);
                            $m->CONTENT_MESSAGE = $message;
                            $m->DISPLAY_=1;
                            $m->DATE_MESSAGE = date("Y-m-d h:i:s");
                            MessageEntity::add(new SQLFactory(),$m);
                            header("Location:$url");
                            break;
                        }
                    }       
                }
            }else{
                Main::redirectWithAlert($template,"Vous devez vous connecter pour accèder à cette section","connexion");
            }
            }else{
                $template->addStylesheet("_css/alert.css");
                $template->addScript("_js/alert.js");
                Main::redirectWithAlert($template,"Vous devez vous connecter pour accèder à cette section","connexion");
            }
      
            
        }
        public function handleRequest(){
            $config=parse_ini_file("include/config.ini",true)["localhost"];
            $template = new EasyTemplate($config,new ResourceManager());
            $sessionManager=EasyGlobal::createSessionManager();
            $template->addStylesheet("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");
            $template->addStylesheet("_css/sujet.css");
            $template->addStylesheet("_css/alert.css");
                $template->addScript("_js/alert.js");
         //  $this->setData("user",)
            if(isset($_GET["id"]) && !isset($_GET["act"])){
                
                $template->remplaceTemplate("MainContent","sujet.tpl");
                $sujet=SujetEntity::getSujetTblBy(new SQLFactory(),"ID_SUJET",$_GET["id"]);
                $sujet->DATE_CLOTURE=$sujet->DATE_CLOTURE??"NULL";
                $this->setData("SUJET",$sujet->getArray());
                /*SQL View*/
                $SQL2V = new SQLtoView(new SQLFactory(),"./sqlView/topic/messageBySujet.view");
                $param=[];
                $param["query"] = "SELECT (SELECT COUNT(*) FROM reponse_tbl r WHERE r.ID_MESSAGE=m.ID_MESSAGE) as NB_REPONSE, DISPLAY_,ID_MESSAGE,AVATAR_USER, CONTENT_MESSAGE,m.ID_USER,PSEUDO_USER,DATE_FORMAT(DATE_MESSAGE,\"%d/%m/%Y\") AS _DATE FROM message_tbl m INNER JOIN utilisateur_tbl u ON  u.ID_USER=m.ID_USER WHERE ID_SUJET={$_GET['id']}";
                $param["callback"] = function (&$item, $previousItem, $defaultString) {
                    if ($item["DISPLAY_"] == 0) {
                        $item["CONTENT_MESSAGE"] = "<em>Message masqué par la modération</em>";
                    } else {
                        $item["CONTENT_MESSAGE"] = str_replace("[b]", "<b>", $item["CONTENT_MESSAGE"]);
                        $item["CONTENT_MESSAGE"] = str_replace("[/b]", "</b>", $item["CONTENT_MESSAGE"]);
                        $item["CONTENT_MESSAGE"] = str_replace("[i]", "<i>", $item["CONTENT_MESSAGE"]);
                        $item["CONTENT_MESSAGE"] = str_replace("[/i]", "</i>", $item["CONTENT_MESSAGE"]);
                        $item["CONTENT_MESSAGE"] = str_replace("\n", "<br>", $item["CONTENT_MESSAGE"]);
                    }
                    return $defaultString;
                };
                $template->_view("allMessage", $SQL2V, $param);
                /*Afficher les réponses pour chaque messages que la vue a générés*/
                $message=MessageEntity::getMessageTblBy(new SQLFactory(),"ID_SUJET", $_GET['id']);
                if($message)
                foreach ($message as $m) {
                    $reponse = $m->getReponse(new SQLFactory());
                    //    var_dump($reponse);
                    $i = 0;
                    $return = (gettype($reponse) == "array") ? $reponse : [$reponse];
                    $sqlF=new SQLFactory();
                    $r = SqlEntities::getArrayEntities($return, function ($carry, $item) use (&$i, $sqlF) {

                        $carry[$i] = $item->getArray();
                        if(!$item->DISPLAY)
                        $carry[$i]["CONTENT_REPONSE"]="Message supprimer par la modération";
                        preg_match("/([0-9]{4}-[0-9]{2}-[0-9]{2})/i", $item->DATE_REPONSE, $matches);
                        $carry[$i]["SHORT_DATE"] = $matches[1];
                        $carry[$i]["LOGIN"] = UtilisateurEntity::getUtilisateurTblBy($sqlF, "ID_USER", $item->ID_USER)->PSEUDO_USER;

                        $i++;

                        return $carry;
                    });

                    if (count($r)) {
                        //  var_dump($r);
                        $template->setLoop("reponse_" . $m->ID_MESSAGE, $r);
                    }
                   
                }
                $template->getRessourceManager()->addDirectJs("window.addEventListener('load', () => {
    // Cache the selectors to avoid querying the DOM multiple times
    const formTargets = document.querySelectorAll('[form-target]');
    const dataTargets = document.querySelectorAll('[data-target]');
    const message = document.querySelector('#messageInput');
    const length = document.getElementById('lengthMessage');

    // Hide all elements with 'form-target' attribute initially
    formTargets.forEach(el => {
        el.style.display = 'none';
    });

    // Add click event listener to elements with 'data-target' attribute
    dataTargets.forEach(el => {
        el.onclick = function () {
            const target = el.getAttribute('data-target');
            const targetElement = document.querySelector(`[form-target='\${target}']`);
            if (targetElement) {
                targetElement.style.display = 'block';
            }
            return false;
        };
    });

    // Add focus and keyup event listeners to the message input
    if (message && length) {
        message.addEventListener('focus', () => {
            message.classList.remove('unvalide');
        });

        message.addEventListener('keyup', () => {
            length.innerHTML = `\${message.value.length}/255`;
        });
    }
});
");
            }else{
                if(isset($_GET["act"])){
                    switch($_GET["act"]){
                        case "close":{
                            $template->addStylesheet("_css/alert.css");
                            $template->addScript("_js/alert.js");
                            //Get Subject
                            $subjet=SujetEntity::getSujetTblBy(new SQLFactory(),"ID_SUJET",$_GET["id"]);
                            //Update date cloture
                            $subjet->DATE_CLOTURE=date("Y-m-d h:i:s");
                            //Commit updates
                            SujetEntity::update(new SQLFactory(),$subjet);
                            Main::redirectWithAlert($template,"Votre sujet a été clos","MesSujets");
                            break;
                        }
                        case "see":{
                            $template->remplaceTemplate("MainContent","mySubject.tpl");
                            $u=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
                            $sqlF=new SQLFactory();
                            $subject=$u->getSujets($sqlF);
                            $i=0;
                            if($subject==false){
                                $template->cancelLoop("MySubject","Vous n'avez pas de sujets");
                                break;
                            }
                            if(gettype($subject)!="array"){
                                $subject=[$subject];
                            }
                            $template->setLoop("MySubject",array_reduce($subject,function($car,$item) use(&$i,$sqlF){
                                $car[$i]=$item->getArray();
                                $m=$item->getMessages($sqlF);
                                $t=$item->getTheme($sqlF);
                               //EasyFrameWork::Debug($t);
                                $car[$i]["THEME"]=$t->LIB_THEME;
                                if($m) {
                                    $car[$i]["NB_MESSAGE"]=count($m);
                                    $car[$i]["LAST_MESSAGE"]=end($m)->DATE_MESSAGE;
                                }
                                else{
                                    $car[$i]["LAST_MESSAGE"]="";
                                    $car[$i]["NB_MESSAGE"]="0";
                                }
                                $i++;
                                return $car;
                            },[]));
                            
                           // EasyFrameWork::Debug($subject);
                            break;
                        }
                        case "add":{
                           
                            $u=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
                          $user=$u->getArray();
                            $sujet=new SujetTbl;
                            $sujet->LIB_SUJET=$_POST["TITRE_SUJET"];
                            $sujet->URL_SUJET=$_POST["URL_SUJET"];
                            $sujet->DATE_SUJET=date("Y-m-d");
                            $sujet->ID_THEME=$_POST["ID_THEME"];
                            $sujet->ID_USER=$user['ID_USER'];
                            $sujet->DISPLAY_SUJET="0";
                            $add=SujetTbl::add(new SQLFactory(),$sujet);
                           // EasyFrameWork::Debug($add);
                            if($add){
                                Main::redirectWithAlert($template,"Votre sujet a été créé","index.php");
                            }else{
                                Main::redirectWithAlert($template,"Une erreur s'est produite, veuillez contacter votre mutuelle","index.php");
                            }
                            break;
                        }
                    case "all":{
                        $template->remplaceTemplate("MainContent","sujetList.tpl");
                
                $sqlF=new SQLFactory();
                $sujetList=SujetEntity::getAll($sqlF);
               // EasyFrameWork::Debug($sujetList);
                $template->setLoop("sujet",array_reduce($sujetList,function($c,$e) use(&$i,$sqlF){

                    if($e->DISPLAY_SUJET=='1'){
                         $c[$i]=$e->getArray();
                    $u=$e->getUtilisateur($sqlF);
                    $last=$e->lastMessage($sqlF);
                    $c[$i]["LAST_MESSAGE_DATE"]=explode(' ',$last->DATE_MESSAGE)[0];
                    $c[$i]["LAST_MESSAGE_CONTENT"]=$last->CONTENT_MESSAGE;
                    $user=UtilisateurEntity::getUtilisateurTblBy($sqlF,"ID_USER",$last->ID_USER);
                    $c[$i]["LAST_MESSAGE_USER"]=$user->PSEUDO_USER;
                    if($u===false){
                        $u=["PSEUDO_USER"=>"LA MODERATION"];
                    }else
                        $u=$u->getArray();
                    $c[$i]["LOGIN_UTILISATEUR"]=$u["PSEUDO_USER"];
                    $c[$i]["ID_SUJET"]=strval($e->ID_SUJET);
                    $nbMess=$e->getMessages($sqlF);
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
                    $c[$i]["THEME_NAME"]=$e->getTheme($sqlF)->LIB_THEME;
                    $i++;
                }
                    return $c;
                },[]));
                 $template->getRessourceManager()->addDirectJs("window.addEventListener('load', () => {
                 document.querySelectorAll('.accordion-collapse.collapse')[0].classList.add('show');
            })");
                        break;
                    }
                    case "new":{
                        $template->remplaceTemplate("MainContent","newSubject.tpl");
                        $template->setLoop("Theme",array_reduce(ThemeTbl::getAll(new SQLFactory()),function($c,$e){
                            $c[]=$e->getArray();
                            return $c;
                        },[]));
                        $template->getRessourceManager()->addDirectJs("window.addEventListener('load', () => {
                            document.querySelector('#sujet_title').onkeyup=function(){
                            let v=this.value;
                            v=v.replaceAll(' ','_');
                            v=v.replaceAll('é','e');
                            v=v.replaceAll('à','a');
                            v=v.replaceAll('è','e');
                            v=v.replaceAll('ë','e');
                            v=v.replaceAll('ê','e');
                            v=v.replaceAll('î','i');
                            v=v.replaceAll('ï','i');
                            document.querySelector('#sujet_url').value=v;
                            };
                       })");
                        break;
                    }
                }
                }
                
            }
                $template->setVariables($this->getData());
                // Rendre le template
                $template->render();
        }
    }