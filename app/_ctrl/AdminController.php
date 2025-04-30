<?php
namespace vendor\easyFrameWork\Core\Master\Controller;


use SQLEntities\OrganismeEntity;
use SQLEntities\PlanningEntity;
use SQLEntities\StateuserTbl;
use vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\ResourceManager;
use vendor\easyFrameWork\Core\Master\EasyTemplate;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Master\SqlToForm;
use SQLEntities\SujetEntity;
use SQLEntities\SujetTbl;
use SQLEntities\ThemeTbl;
use SQLEntities\TypeUtilisateur;
use SQLEntities\UtilisateurEntity;
use SQLEntities\UtilisateurTbl;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use DateTime;
use SQLEntities\ExchangeEntity;

class AdminController extends Controller{
    private $user;
    private $activPage=[];
    private $template=null;
    public function __construct($env = null){
        parent::__construct($env);
        $this->user=null;
            $sessionManager=EasyGlobal::createSessionManager();
           $isConnect=(($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT))!=null)?"1":"0";
        $this->setData("isConnect",$isConnect);
        if($isConnect=="1"){
            $u=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
            $this->user=$u;
            $user=$u->getArray();
          //  EasyFrameWork::Debug($u->getRole(new SQLFactory()));
          $user["NOM_ORGANISME"]=$user["ORGANISME"]!=false?$user["ORGANISME"]:"-";
            $this->setData("user",$user);
        }
    }
    public function addUser(){
    $data=[];
    $data["nom"]=$_POST["DATA_VALUE"]["nom"];
    $data["prenom"]= $_POST["DATA_VALUE"]["prenom"];
    $data["droit"]=[];
    $data["droit"]["User"]=$_POST["DATA_VALUE"]["droitUser"];
    $data["droit"]["Sujet"]=$_POST["DATA_VALUE"]["droitSujet"];
    $data["droit"]["Exchange"]=$_POST["DATA_VALUE"]["droitExchange"];
    $insertData=addslashes(json_encode($data));
         $u=new UtilisateurTbl();
         $u->MAIL_USER=$_POST["MAIL_USER"];
         $u->PSEUDO_USER=$_POST["PSEUDO_USER"];
         $u->TYPE_UTILISATEUR=$_POST["TYPE_UTILISATEUR"];
         $u->ARRIVE_UTILISATEUR=date("Y-m-d");
         $u->ID_ORGA=$this->user->ID_ORGA;
         $u->VALID_USER=0;
         $u->DATA_USER=$insertData;
         if( UtilisateurTbl::add(new SQLFactory(),$u)){
          $url="UserGestion";
          $this->template->addStylesheet("_css/alert.css");
           Main::redirectWithAlert($this->template,"L\'utilisateur a été ajouté",$url);
         }
    }
    public function displayUser(){

        $u= UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(),"ID_USER",$_GET["id"]);
     //   EasyFrameWork::Debug($u->ID_ORGA);
        if($this->user->ID_ORGA!==$u->ID_ORGA){
            
            $url="UserGestion";
            Main::redirectWithAlert($this->template,"Vous n\'avez pas les droits pour accèder à cet utilisateur",$url);
            return;
        }
        $i=0;
        $statArr=$u->getState(new SQLFactory());
        $stat=[[
            "DATE"=>"-",
            "MOTIF"=>"-",
            "LIBELLE"=>"-"
        ]];
        if(gettype($statArr)=="array"){
        $stat=array_reduce($statArr,function($c,$e) use(&$i){
            $c[$i]["DATE"]=$e->DATE_STATE;
            $c[$i]["MOTIF"]=$e->MOTIF_STATE??"-";
            $c[$i]["LIBELLE"]=StateuserTbl::getStateuserTblBy(new SQLFactory(),"ID_STATE",$e->STATE)->LIBELLE_STATE;
            $i++;
            return $c;
        },[]);
        usort($stat,function($a,$b){
           return Main::DateCompare($a["DATE"],$b["DATE"]);
        });
    }

                                    $this->setData("UPDATE",$u->getArray());
                                    $i=0;
                                    $type=array_reduce(TypeUtilisateur::getAll(new SQLFactory()),function($c,$e) use(&$i){
                                        $c[$i]=$e->getArray();
                                        $c[$i]["idType_Utilisateur"]=strval($e->idType_Utilisateur);
                                        $i++;
                                        return $c;
                                    },[]);
                                    if($u->TYPE_UTILISATEUR!="2")
                                        $this->template->setLoop("DATAS",$u->getData());
                                    else{
                                        $i=0;
                                        $dataDisplay=[];
                                        $dataDisplay[0]["KEY"]="nom";
                                        $dataDisplay[0]["VALUE"]=$u->getData("nom");
                                        $dataDisplay[1]["KEY"]="prenom";
                                        $dataDisplay[1]["VALUE"]=$u->getData("prenom");
                                        $i=2;
                                     //   EasyFrameWork::Debug($u->getData("droit"));
                                        foreach($u->getData("droit") as $k=>$v){
                                            $dataDisplay[$i]["KEY"]=$k;
                                            $dataDisplay[$i]["VALUE"]=$v;
                                            $i++;
                                        }
                                       // EasyFrameWork::Debug($dataDisplay);
                                        $this->template->setLoop("DATAS",$dataDisplay);
                                    }
                                    $this->template->setLoop("type_utilisateur",$type);
                                   // EasyFrameWork::Debug($stat);
                                    $this->template->setLoop("StatUser",$stat);
                                    $this->template->getRessourceManager()->addDirectJs("$(function(){
                                    document.querySelector(\"[idUser='".$_GET["id"]."']\").parentNode.parentNode.classList.add('table-info');
                                    });");
    }
    private function checkAccess($module) {
        $rights = $this->user->getRights($module);

        if (!$rights) {
            echo "Vous n'avez pas les droits nécessaires pour accéder à cette page";
            exit();
        }

        return true;
    }
    private function prepareSujetData() {
        $sujet = SujetEntity::getAll(new SQLFactory());
        $sqlF = new SQLFactory();

        $sujetList = array_reduce($sujet, function ($c, $e) use ($sqlF) {
            $u = $e->getUser($sqlF);
            $data = $e->getArray();
            $data["ID"] = strval($e->ID_SUJET);
            $data["DISPLAY_SUJET"]=strval($e->DISPLAY_SUJET);
            if ($u) {
                $data["PSEUDO_USER"] = $u->PSEUDO_USER;
                $data["NOM_ORGANISME"] = $u->getOrganisme($sqlF)->NOM_ORGANISATION ?? "-";
            } else {
                $data["PSEUDO_USER"] = "-";
                $data["NOM_ORGANISME"] = "-";
            }

            $c[] = $data;
            return $c;
        }, []);

        $themeList = array_map(function ($theme) {
            return $theme->getArray();
        }, ThemeTbl::getAll($sqlF));

        return [
            "sujetList" => $sujetList,
            "themeList" => $themeList
        ];
    }

    private function handleUser() {
        if (!$this->checkAccess(module: "User")) {
            return;
        }
        $rights=$this->getUserRight("User");
        $this->activPage["USER"] = " activ";
        $this->setData("activPage", $this->activPage);
        $this->setData("droit", $rights);
        $users = $this->user->ID_ORGA
            ? UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(), "ID_ORGA", $this->user->ID_ORGA)
            : UtilisateurEntity::getAll(new SQLFactory());

        $this->template->remplaceTemplate("MainContent", "ADMIN/user.tpl");
        $this->template->setLoop("userList", $this->formatUserList($users));
        $this->template->addStylesheet("_css/form.css");
        $this->template->getRessourceManager()->addDirectJs("$(function(){

           const banTriggers=document.querySelectorAll(\"[data-bs-target='#BannedUser']\");
           banTriggers.forEach(banTrigger=>{
                banTrigger.addEventListener('click',function(){
                document.getElementById('BanComment').value='';
                    this.parentNode.parentNode.classList.add('table-info');
                    let id=this.getAttribute('idUser');
                    getUserInfo(id);
           });
    });
            const myModalEl = document.getElementById('BannedUser')
myModalEl.addEventListener('hidden.bs.modal', event => {
  document.querySelector('tr.table-info').classList.remove('table-info');
  document.querySelector(`[data-async=ID_USER]`).innerHTML='';
})



        });");
        if(isset($_GET["act"])){
            switch($_GET["act"]){
                case "see":
                    $this->displayUser();
                    break;
                case "add":
                    $this->addUser();
                    break;
                    case "UserCheck":{                  
                    $users=UtilisateurTbl::getAll(new SQLFactory());
                    array_walk($users,function($e){
                        if($e->ID_USER!=$this->user->ID_USER){
                            $now=date("Y-m-d");
                            var_dump( Main::DateCompare($e->DATE_VALIDITE,$now));
                            if($e->DATE_VALIDITE!="" && Main::DateCompare($e->DATE_VALIDITE,$now)==0){
                                $e->VALID_USER=false;
                                UtilisateurTbl::update(new SQLFactory(),$e);
                            }else{
                                $e->VALID_USER=1;
                                UtilisateurTbl::update(new SQLFactory(),$e);
                            }
                        }
                    });
                    $url="UserGestion";
                    Main::redirectWithAlert($this->template,"Les données de l\utilisateur on été mise à jour",$url);
                    header("Location:UserGestion");
                    break;
                }
            }
        }
    }

    private function formatUserList($users) {
        $i = 0;
        return array_reduce($users, function ($c, $e) use (&$i) {
            if ($e->ID_USER != $this->user->ID_USER) {
                $c[$i] = $e->getArray();
                $c[$i]["ID_USER"] = strval($e->ID_USER);
                $c[$i]["TYPE_UTILISATEUR"]=strval($e->TYPE_UTILISATEUR);
                $c[$i]["VALID_USER"] = strval($e->VALID_USER);
                $c[$i]["USER_TYPE"] = $e->getRole(new SQLFactory())->LIBELLE_Type_Utilisateur;
                $i++;
            }
            return $c;
        }, []);
    }
    private function handleSujet() {
        if (!$this->checkAccess("Sujet")) {
            return;
        }
        $rights=$this->getUserRight("Sujet");
  //      EasyFrameWork::Debug($rights);
        $this->activPage["SUJET"] = " activ";
        $this->setData("activPage", $this->activPage);
        $this->setData("droit", $rights);
        $templateData = $this->prepareSujetData();
        $this->template->setLoop("sujetList", $templateData["sujetList"]);
        $this->template->setLoop("Theme", $templateData["themeList"]);

        $this->template->remplaceTemplate("MainContent", "ADMIN/sujet.tpl");
        $this->template->getRessourceManager()->addDirectJs("window.addEventListener('load',function(){
                             document.querySelectorAll('[idSujet]').forEach(function(e){
                                 e.onclick=function(){
                                 let title=this.parentNode.parentNode.children[1].innerHTML;
                                 document.querySelector('#SeeMessageLabel').innerHTML=title;
                                let url= document.querySelector('#export').getAttribute('href');
                                url+=this.getAttribute('idSujet');
                                document.querySelector('#export').setAttribute('href',url);
                                     getMessages(this.getAttribute('idSujet'));
                                 }
                             });
                         });");
        if(isset($_GET["id"])){
            $this->setSujet($this->template);
         }
         $sqlF=new SQLFactory();
         if(isset($_GET["act"])){
            switch($_GET["act"]){
                case "update":
                    $this->updateSujet(SujetEntity::getSujetTblBy($sqlF,"ID_SUJET",$_GET["id"]),$this->template);
                }
            }
    }
    public function UpdateUSer(UtilisateurEntity $userEntity,&$template){
      //  $userEntity->MAIL_USER=$postData["MAIL_USER"];
       // $userEntity->PSEUDO_USER=$postData["PSEUDO_USER"];
     //   $u->TYPE_UTILISATEUR=$postData["TYPE_UTILISATEUR"];
     //var_dump($_POST);
        $nbData=count($_POST["DATA_KEY"]);
        $data=[];
        for($i=0;$i<$nbData;$i++){
            $data[$_POST["DATA_KEY"][$i]]=$_POST["DATA_VALUE"][$i];
        }
        $userEntity->DATA_USER=addslashes(json_encode($data));
        $Updateuser=Main::fixObject($userEntity,"SQLEntities\UtilisateurTbl");
        UtilisateurTbl::update(new SQLFactory(),$Updateuser);
        $url="UserGestion-see_{$_GET["id"]}";
                                    $template->addStylesheet("_css/alert.css");
                                    Main::redirectWithAlert($template,"Les données de l\utilisateur on été mise à jour",$url);
       
    }
    private function updateSujet(SujetEntity $sujetEntity,EasyTemplate &$template){
        //EasyFrameWork::Debug($_POST);
        $template->addStylesheet("_css/alert.css");
        $sujetEntity->LIB_SUJET=$_POST["LIB_SUJET"];
        $sujetEntity->URL_SUJET=$_POST["URL_SUJET"];
        $sujetEntity->ID_THEME=$_POST["ID_THEME"]??"-";
        $sujetEntity->DATE_CLOTURE=$_POST["DATE_CLOTURE"];
        $sujetEntity->DISPLAY_SUJET=isset($_POST["DISPLAY_SUJET"])?"1":"0";
        $updateSujet=Main::fixObject($sujetEntity,"SQLEntities\SujetTbl");
        SujetTbl::update(new SQLFactory(),$updateSujet);
        Main::redirectWithAlert($template,"Les mises à jour on été faite sur ce sujet","SujetGestion");
    }
    private function setSujet(&$template){
      //  EasyFrameWork::Debug($_GET["id"]);
        $sujet=SujetEntity::getSujetTblBy(new SQLFactory(),"ID_SUJET",$_GET["id"]);
       // EasyFrameWork::Debug($sujet);
        $s=$sujet->getArray();
        $u=$sujet->getUser(new SQLFactory());
        $s["PSEUDO_USER"]=$u!=false?$u->PSEUDO_USER:"-";
        $this->setData("UPDATE",$s);
        $i=0;
        $sqlF=new SQLFactory();
        $template->setLoop("Theme",array_reduce(ThemeTbl::getAll($sqlF),function($c,$e) use(&$i,$s){
            $c[$i]=$e->getArray();
            $c[$i]["SELECTED"]=($s["ID_THEME"]==$e->ID_THEME)?"selected":"";
            $i++;
            return $c;
        },[]));
    }
    private function checkUserAccess() {
        if (!$this->user || $this->user->TYPE_UTILISATEUR == "1") {
            $this->template->addStylesheet("_css/alert.css");
         //   $this->template->renderAlert("Vous n'avez pas les droits nécessaires pour accéder à cette page", "index.php");
            return false;
        }
        return true;
    }
    private function getHistorique($exchange){
        return array_reduce($exchange,function($c,$e)use(&$i){
            $now = new DateTime();
            $exchangeDate=new DateTime($e->dateExchange);
            if($exchangeDate<$now){
                $s=["DEFAULT","RUN","CLOSURE","ERROR"];
                $exchangeEl=$e->getArray();
                $data=[];
                $data["item"]=$e;
                $data["PSEUDO_USER"]=$exchangeEl["UtilisateurEntity"]["PSEUDO_USER"];
                $data["HEURE"]=$exchangeEl["HeureDebut"];
                $data["JOUR"]=$exchangeEl["dateExchange"];
                $indexS=($exchangeEl["StateExchange"])??0;
                $data["STATUT"]=$s[$indexS];
                if($m=$e->getMessages(new SQLFactory())){
                    if(gettype($m)=="array")
                    $data["NBMESSAGE"]="".count($m)."";
                else
                $data["NBMESSAGE"]="1";

                   
                }else
                $data["NBMESSAGE"]="0";
                
                $c[$i++]=$data;
            }
            return $c;
        },[]);
    }
    private function handleExchange() {
        $droits = $this->user->getRights();

        if (!isset($droits["Exchange"])) {
            echo "Vous n'avez pas les droits nécessaires pour accéder à cette page";
            exit();
        }
        $orga = $this->user->getOrganisme(new SQLFactory());
        $exchange = $orga->getExchange(new SQLFactory());
        date_default_timezone_set('Europe/Paris');
        $this->activPage["EXCHANGE"] = " activ";
        $this->setData("activPage", $this->activPage);
       if(isset($_GET["act"])){
        switch($_GET["act"]){
            case "purge":{
                $histo=$this->getHistorique($exchange);
                $sqlF=new SQLFactory();
                array_walk($histo,function($el) use($sqlF){
                    EasyFrameWork::Debug([$el["STATUT"],$el["NBMESSAGE"]],false);
                    if($el["STATUT"]=="DEFAULT" && $el["NBMESSAGE"]=="0"){
                        ExchangeEntity::del($sqlF,$el["item"]);
                    }
                });
                Main::redirectWithAlert($this->template,"Les echanges ont été purgés !","ExchangeGestion");
                break;
            }
            case "addPlanning":{
                echo "addPlanning";
                $post=EasyGlobal::createQuery();
                if(!isset($post)){
                    Main::redirectWithAlert($this->template,"Une erreur s'est produite !","ExchangeGestion");
                }
            //    EasyFrameWork::Debug($post);
                $p=new PlanningEntity;
                $p->jouer_semaine=$post->get("jouer_semaine");
                $p->heure_debut=$post->get("heure_debut");
                $p->heure_fin=$post->get("heure_fin");
                $p->organisme_tbl_ID_ORGA=$orga->ID_ORGA;
                PlanningEntity::add(new SQLFactory(),$p);
                break;
            }
        }
       }
        
        
        
        $exchangeList=self::formatExchangeList($exchange);
        $histo=array_reduce($exchange,function($c,$e)use(&$i){
            $now = new DateTime();
            $exchangeDate=new DateTime($e->dateExchange);
            if($exchangeDate<$now){
                $s=["DEFAULT","RUN","CLOSURE","ERROR"];
                $exchangeEl=$e->getArray();
                $data=[];
                $data["PSEUDO_USER"]=$exchangeEl["UtilisateurEntity"]["PSEUDO_USER"];
                $data["HEURE"]=$exchangeEl["HeureDebut"];
                $data["JOUR"]=$exchangeEl["dateExchange"];
                $indexS=($exchangeEl["StateExchange"])??0;
                $data["STATUT"]=$s[$indexS];
                if($m=$e->getMessages(new SQLFactory())){
                    if(gettype($m)=="array")
                    $data["NBMESSAGE"]="".count($m)."";
                else
                $data["NBMESSAGE"]="1";

                   
                }else
                $data["NBMESSAGE"]="0";
                
                $c[$i++]=$data;
            }
            return $c;
        },[]);
      //  EasyFrameWork::Debug($histo);
        $this->template->remplaceTemplate("MainContent", "ADMIN/exchange.tpl");
        $this->template->setLoop("histoExchange", $histo);
        $this->template->addStylesheet("_css/exchange.css");
      $this->template->addScript("_js/exchange.js");

        ContactController::renderAgendaWithSlots($orga,$this->template);
        $this->template->setLoop("exchangeList", $exchangeList);
        //Formulaire d'ajout
            $frm=new SqlToForm(new SQLFactory());
    $addForm=$frm->generate(["URI"=>"ExchangeGestion-addPlanning",
    "label"=>true,
    "METHOD"=>"POST",
    "table"=>"planning_tbl",
    "ignoreFields"=>["idPlanning","organisme_tbl_ID_ORGA"]]);
    $addForm=str_replace("&lt;","<",$addForm);
    $this->setData("addForm|rawHTML",($addForm));
        $this->template->getRessourceManager()->addDirectJs("$(function(){
        $('[data-exchange]').click(function(){
        $('[name=chat]').css('display','block');
        $('[name=chat]').attr('UUID',$(this).attr('data-exchange'));
        $('[name=chat]>h5>span').html($(this).attr('data-exchange'));
        getMessage($(this).attr('data-exchange'),function(data){
            exchangeListener(data,{var:user.ID_USER})
    },1000);
    });
        let links=document.querySelectorAll('.nav-pills .nav-item a');
        let targets=['Planning','ExchangeList','chat'];
        function resetActive(){
        links.forEach(el=>{el.classList.remove('active')});
        targets.forEach(t=>{document.querySelector('[name='+t+']').style.display='none'});

        }
        resetActive();
        links[0].classList.add('active');
        document.querySelector('[name='+targets[0]+']').style.display='flex';
        links.forEach(el=>{
        el.onclick=function(){
        let t=$(this).attr('_target');
       
        resetActive();
            this.classList.add('active');
            document.querySelector('[name='+t+']').style.display='flex';
        }
            
        });
        });");
    }

    public static function formatExchangeList($exchange) {
        $i = 0;
    
        usort($exchange, function($a, $b) {
            $aTime = new DateTime($a->dateExchange . ' ' . self::normalizeTime($a->HeureDebut));
            $bTime = new DateTime($b->dateExchange . ' ' . self::normalizeTime($b->HeureDebut));
            return $aTime <=> $bTime;
        });
    
        return array_reduce($exchange, function ($c, $e) use (&$i) {
            $now = new DateTime();
            $heure = self::normalizeTime($e->HeureDebut);
            $exchangeDateTime = new DateTime($e->dateExchange . " " . $heure);
            $tolerance = (clone $now)->modify('-10 minutes');
           // EasyFrameWork::Debug([$exchangeDateTime,$now],false);
            if ($exchangeDateTime >= $tolerance) {
                $data = $e->getArray();
                $data["NOW"] = 0;
    
                // Marque comme "en cours" si dans les 5 dernières minutes
                if ($exchangeDateTime <= $now && $exchangeDateTime >= $tolerance) {
                    $data["NOW"] = "1";
                }
    
                $data["LOGIN_USER"] = $data["UtilisateurEntity"]["PSEUDO_USER"];
                unset($data["UtilisateurEntity"]);
    
                $c[$i++] = $data;
            }
    
            return $c;
        }, []);
    }
    
    // Normalise l'heure au format H:i:s
    private static function normalizeTime($time) {
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return $time . ":00";
        }
        return $time;
    }
    
    
    

    public function handleRequest() {
        if (!$this->checkUserAccess()) {
            return;
        }
        $config=parse_ini_file("include/config.ini",true)["localhost"];
             $this->template = new EasyTemplate($config,new ResourceManager());
             $this->template->addStylesheet("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");
        $action = $_GET["root"] ?? null;

        switch ($action) {
            case "exchange":
                $this->handleExchange();
                break;

            case "sujet":
                $this->handleSujet();
                break;

            case "user":
                $this->handleUser();
                break;
        }

        $this->template->setVariables($this->getData());
        $this->template->render();
    }
    private function getUserRight($access){
        $r = $this->user->getRights($access);
     //   EasyFrameWork::Debug($r);
$rights =[
"User"=> [
    "Create" => "0",
    "Suppr" => "0",
    "Update" => "0",
    "Lecture" => "0",
    "Modarate" => "0"
],
"Sujet"=> [
    "Create" => "0",
    "Suppr" => "0",
    "Update" => "0",
    "Lecture" => "0"
],
"Exchange"=> [
    "E" => "0",
    "L" => "0",
    "A" => "0"]
];

$permissionsMap = [
    "User"=>[
    "C" => "Create",
    "S" => "Suppr",
    "U" => "Update",
    "L" => "Lecture",
    "M" => "Modarate"
    ],
    "Exchange"=>[
        "E"=>"Ecriture",
        "L"=>"Lecture",
        "A"=>"Admin"
    ],
    "Sujet"=>[
        "C" => "Create",
    "S" => "Suppr",
    "U" => "Update",
    "L" => "Lecture",
        ]
];

foreach ($r as $right) {
    if (array_key_exists($right, $permissionsMap[$access])) {
        $rights[$access][$permissionsMap[$access][$right]] = "1";
    }
}
return $rights[$access];
    }
    // public function handleRequest(){
       
    //                 $droits=$this->user->getRights();
                   
    //     $config=parse_ini_file("include/config.ini",true)["localhost"];
    //         $template = new EasyTemplate($config,new ResourceManager());
    //         $template->addStylesheet("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");
    //         /*Close Guard */
    //         if($this->user==null || $this->user->TYPE_UTILISATEUR=="1"){
    //             $template->addStylesheet("_css/alert.css");
    //             Main::redirectWithAlert($template,"Vous n\'avez pas les droits nécessaires pour acceder à cette page","index.php");
                
    //         }
           
    //         switch($_GET["root"]){
    //             case "exchange":{
                    
    //                // EasyFrameWork::Debug($droits);
    //                if(!isset($droits["Exchange"])){
    //                     echo "Vous n'avez pas les droits nécessaires pour accéder à cette page";
    //                     exit();
    //                }
    //                 $this->activPage["EXCHANGE"]=" activ";
    //                 $this->setData("activPage",$this->activPage);
    //                 $template->remplaceTemplate("MainContent","ADMIN/exchange.tpl");
    //                 $orga=$this->user->getOrganisme(new SQLFactory());
    // ContactController::renderAgendaWithSlots($orga,$template);
    // $frm=new SqlToForm(new SQLFactory());
    // $addForm=$frm->generate(["URI"=>"addPlanning",
    // "label"=>true,
    // "METHOD"=>"POST",
    // "table"=>"planning_tbl",
    // "ignoreFields"=>["idPlanning","organisme_tbl_ID_ORGA"]]);
    // $addForm=str_replace("&lt;","<",$addForm);
    // $this->setData("addForm|rawHTML",($addForm));
    //                 $exchange=$orga->getExchange(new SQLFactory());
    //                 $template->setLoop("exchangeList",array_reduce($exchange,function($c,$e) use(&$i){
    //                     if($e->dateExchange==date("Y-m-d")){
    //                         $c[$i]=$e->getArray();
    //                         $c[$i]["LOGIN_USER"]=$e->getArray()["UtilisateurEntity"]["PSEUDO_USER"];
    //                         $i++;
    //                     }
                            
    //                     return $c;
    //                 },[]));
    //                 break;
    //             }
    //             case "sujet":{
    //                 if($this->user->getRights("Sujet")==false){
    //                     echo "Vous n'avez pas les droits nécessaires pour accéder à cette page";
    //                     exit();
    //                 }else{
    //                     $r=$this->user->getRights("Sujet");
    //                     $rights=[
    //                         "Create"=>"0",
    //                         "Suppr"=>"0",
    //                         "Update"=>"0",
    //                         "Lecture"=>"0",
    //                     ];
    //                     for($i=0;$i<4;$i++){    
    //                         if(isset($r[$i])){
    //                         if($r[$i]=="C")
    //                             $rights["Create"]="1";
    //                         elseif($r[$i]=="S")
    //                             $rights["Suppr"]="1";
    //                         elseif($r[$i]=="U")
    //                             $rights["Update"]="1";
    //                             elseif($r[$i]=="L")
    //                             $rights["Lecture"]="1";
    //                         }
    //                     }
    //                 }
    //            //  EasyFrameWork::Debug($rights);
    //                 $this->setData("droit",$rights);
    //                 $this->activPage["SUJET"]=" activ";
    //                 $this->setData("activPage",$this->activPage);
    //                 $template->remplaceTemplate("MainContent","ADMIN/sujet.tpl");
    //                 $template->addStylesheet("_css/form.css");
    //                // $template->addStylesheet("_css/alert.css");
    //                $sujet=SujetEntity::getAll(new SQLFactory());
    //                $sqlF=new SQLFactory();
    //                $i=0;
    //                $arr=array_reduce($sujet,function($c,$e) use(&$i,$sqlF){
    //         //        EasyFrameWork::Debug($e->getArray()["ID_SUJET"],false);
    //                 $u=$e->getUser($sqlF);
    //           //      EasyFrameWork::Debug(gettype($this->user->ID_ORGA));
    //                  $c[$i]=$e->getArray();
    //                  if(isset($this->user->ID_ORGA) && $u!=false   && $u->ID_ORGA==$this->user->ID_ORGA){
    //                     $c[$i]["ID"]=strval($e->ID_SUJET);
    //                     $c[$i]["ID_SUJET"]=strval($e->ID_SUJET);
    //                 $t=$e->getTheme($sqlF);
    //                     if($t)
    //                         $c[$i]["LIB_THEME"]=$t->LIB_THEME;
    //                         if($u)
    //                             $c[$i]["PSEUDO_USER"]=$u->PSEUDO_USER;
    //                         else
    //                             $c[$i]["PSEUDO_USER"]="-";
    //                     $i++;
    //                  }else{
    //                     $c[$i]["ID"]=strval($e->ID_SUJET);
    //                     $t=$e->getTheme($sqlF);
    //                         if($t)
    //                             $c[$i]["LIB_THEME"]=$t->LIB_THEME;
                             
    //                             if($u){
    //                                  $c[$i]["PSEUDO_USER"]=$u->PSEUDO_USER;
    //                                  $orga=$u->getOrganisme($sqlF);
                               
    //                                 $c[$i]["NOM_ORGANISME"]=$orga ->NOM_ORGANISATION;
    //                             }else{
    //                                 $c[$i]["NOM_ORGANISME"]="-";
    //                                 $c[$i]["PSEUDO_USER"]="-";
    //                             }
    //                         $i++;
    //                  }
                    
                
    //                 return $c;
    //             },[]);
    //         //   EasyFrameWork::Debug($arr);
    //                $template->setLoop("sujetList",$arr);
    //             $template->setLoop("Theme",array_reduce(ThemeTbl::getAll($sqlF),function($c,$e) {
    //                 $c[]=$e->getArray();
    //                 return $c;
    //             },[]));
    //             $template->getRessourceManager()->addDirectJs("window.addEventListener('load',function(){
    //                 document.querySelectorAll('[idSujet]').forEach(function(e){
    //                     e.onclick=function(){
    //                     let title=this.parentNode.parentNode.children[1].innerHTML;
    //                     document.querySelector('#SeeMessageLabel').innerHTML=title;
    //                    let url= document.querySelector('#export').getAttribute('href');
    //                    url+=this.getAttribute('idSujet');
    //                    document.querySelector('#export').setAttribute('href',url);
    //                         getMessages(this.getAttribute('idSujet'));
    //                     }
    //                 });
    //             });");
    //             if(isset($_GET["act"])){
    //                 switch($_GET["act"]){
    //                     case "update":
    //                         $this->updateSujet(SujetEntity::getSujetTblBy($sqlF,"ID_SUJET",$_GET["id"]),$template);
    //                 }
    //             }
    //             if(isset($_GET["id"])){
    //                 $this->setSujet($template);
    //             }
    //                 break;
    //             }
    //             case "user":
    //                 {
    //                     if($this->user->getRights("Sujet")==false){
    //                         echo "Vous n'avez pas les droits nécessaires pour accéder à cette page";
    //                         exit();
    //                     }else{
    //                         $r=$this->user->getRights("User");
    //                         $rights=[
    //                             "Create"=>"0",
    //                             "Suppr"=>"0",
    //                             "Update"=>"0",
    //                             "Lecture"=>"0",
    //                             "Modarate"=>"0"
    //                         ];
    //                         for($i=0;$i<5;$i++){    
    //                             if(isset($r[$i])){
    //                             if($r[$i]=="C")
    //                                 $rights["Create"]="1";
    //                             elseif($r[$i]=="S")
    //                                 $rights["Suppr"]="1";
    //                             elseif($r[$i]=="U")
    //                                 $rights["Update"]="1";
    //                              elseif($r[$i]=="L")
    //                                 $rights["Lecture"]="1";
    //                             elseif($r[$i]=="M")
    //                                 $rights["Modarate"]="1";
    //                             }
    //                         }
    //                     }
    //                     $this->setData("droit",$rights);
    //                     $this->activPage["USER"]=" activ";
    //                     $this->setData("activPage",$this->activPage);
    //                     $template->remplaceTemplate("MainContent","ADMIN/user.tpl");
    //                     if(isset($_GET["act"])){
    //                         switch($_GET["act"]){
    //                             case "add":{
    //                             //   EasyFrameWork::Debug($_POST);
    //                             $data=[];
    //                             $data["nom"]=$_POST["DATA_VALUE"]["nom"];
    //                             $data["prenom"]= $_POST["DATA_VALUE"]["prenom"];
    //                             $data["droit"]=[];
    //                             $data["droit"]["User"]=$_POST["DATA_VALUE"]["droitUser"];
    //                             $data["droit"]["Sujet"]=$_POST["DATA_VALUE"]["droitSujet"];
    //                             $data["droit"]["Exchange"]=$_POST["DATA_VALUE"]["droitExchange"];
    //                             $insertData=addslashes(json_encode($data));
    //                                 $u=new UtilisateurTbl();
    //                                 $u->MAIL_USER=$_POST["MAIL_USER"];
    //                                 $u->PSEUDO_USER=$_POST["PSEUDO_USER"];
    //                                 $u->TYPE_UTILISATEUR=$_POST["TYPE_UTILISATEUR"];
    //                                 $u->ARRIVE_UTILISATEUR=date("Y-m-d");
    //                                 $u->ID_ORGA=$this->user->ID_ORGA;
    //                                 $u->VALID_USER=0;
    //                                 $u->DATA_USER=$insertData;
    //                               if( UtilisateurTbl::add(new SQLFactory(),$u)){
    //                                 $url="UserGestion";
    //                                 $template->addStylesheet("_css/alert.css");
    //                                 Main::redirectWithAlert($template,"L\'utilisateur a été ajouté",$url);
    //                               }
    //                                 break;
    //                             }
    //                             case "UserCheck":{
                                    
    //                                 $users=UtilisateurTbl::getAll(new SQLFactory());
    //                                 array_walk($users,function($e){
    //                                     if($e->ID_USER!=$this->user->ID_USER){
    //                                         $now=date("Y-m-d");
    //                                         var_dump( Main::DateCompare($e->DATE_VALIDITE,$now));
    //                                         if($e->DATE_VALIDITE!="" && Main::DateCompare($e->DATE_VALIDITE,$now)==0){
    //                                             $e->VALID_USER=false;
    //                                             UtilisateurTbl::update(new SQLFactory(),$e);
    //                                         }else{
    //                                             $e->VALID_USER=1;
    //                                             UtilisateurTbl::update(new SQLFactory(),$e);
    //                                         }
    //                                     }
    //                                 });
    //                                 $url="UserGestion";
    //                                 Main::redirectWithAlert($template,"Les données de l\utilisateur on été mise à jour",$url);
    //                                 header("Location:UserGestion");
    //                                 break;
    //                             }
    //                             case "update":{
    //                                 $u= UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(),"ID_USER",$_GET["id"]);
    //                               //  $json_data = json_decode(file_get_contents('php://input'), true);
    //                               //  var_dump($json_data);
    //                               $nbData=count($_POST["DATA_KEY"]);
    //                               $data=[];
    //                               for($i=0;$i<$nbData;$i++){
    //                                   $data[$_POST["DATA_KEY"][$i]]=$_POST["DATA_VALUE"][$i];
    //                               }
    //                               $u->DATA_USER=addslashes(json_encode($data));
    //                               $Updateuser=Main::fixObject($u,"SQLEntities\UtilisateurTbl");
    //                               UtilisateurTbl::update(new SQLFactory(),$Updateuser);
    //                                 $url="UserGestion-see_{$_GET["id"]}";
    //                                 $template->addStylesheet("_css/alert.css");
    //                                 Main::redirectWithAlert($template,"Les données de l\utilisateur on été mise à jour",$url);
    //                                 header("Location:$url");
    //                                 break;
    //                             }
    //                             case "switch":{
    //                                 $u= UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(),"ID_USER",$_GET["id"]);
    //                                 $u->VALID_USER=($u->VALID_USER=="1")?0:1;
    //                                 UtilisateurEntity::update(new SQLFactory(),Main::fixObject($u,"SQLEntities\UtilisateurTbl"));
    //                                 $template->addStylesheet("_css/alert.css");
    //                                 Main::redirectWithAlert($template,"La validité de l\'utilisateur a été modifiée","UserGestion");
    //                                 break;
    //                             }
    //                             case "see":{
    //                                 $u= UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(),"ID_USER",$_GET["id"]);
                                    
    //                                 $this->setData("UPDATE",$u->getArray());
    //                                 $i=0;
    //                                 $type=array_reduce(TypeUtilisateur::getAll(new SQLFactory()),function($c,$e) use(&$i){
    //                                     $c[$i]=$e->getArray();
    //                                     $i++;
    //                                     return $c;
    //                                 },[]);
    //                                 if($u->TYPE_UTILISATEUR!="2")
    //                                     $template->setLoop("DATAS",$u->getData());
    //                                 else{
    //                                     $i=0;
    //                                     $dataDisplay=[];
    //                                     $dataDisplay[0]["KEY"]="nom";
    //                                     $dataDisplay[0]["VALUE"]=$u->getData("nom");
    //                                     $dataDisplay[1]["KEY"]="prenom";
    //                                     $dataDisplay[1]["VALUE"]=$u->getData("prenom");
    //                                     $i=2;
    //                                  //   EasyFrameWork::Debug($u->getData("droit"));
    //                                     foreach($u->getData("droit") as $k=>$v){
    //                                         $dataDisplay[$i]["KEY"]=$k;
    //                                         $dataDisplay[$i]["VALUE"]=$v;
    //                                         $i++;
    //                                     }
    //                                    // EasyFrameWork::Debug($dataDisplay);
    //                                     $template->setLoop("DATAS",$dataDisplay);
    //                                 }
    //                                 $template->setLoop("type_utilisateur",$type);
                                    
    //                                 $template->getRessourceManager()->addDirectJs("$(function(){\n
    //                                 $('[name=addData]').click(function(){\n
    //                                     addDataLine();
    //                                     this.remove();
    //                                     return false;
    //                             });
    //                             $('[name=removeData]').click(function(e){\n
    //                                 e.preventDefault();\n
    //                                     $(this).parent().parent().remove()\n
    //                                     let t=document.querySelector('#USERDATA>tbody');
    //                                     if(t.children.length==0){
    //                                     let line=document.createElement('tr');
    //                                     line.innerHTML=\"<td colspan=3><a href='#' name='addData' class='btn btn-success'><i class='fa-solid fa-plus'></i></a></td>\"
    //                                     console.log('il faut remettre un bouton');
    //                                     t.appendChild(line);
    //                                     setUserButtons();
    //                             }
    //                                     return false;\n
    //                             });
    //                             })");
    //                                 break;
                                    
    //                             }
    //                         }
    //                     }
    //                    // EasyFrameWork::Debug($this->user);
    //                     if($this->user->ID_ORGA!='')
    //                      $users= UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(),"ID_ORGA",$this->user->ID_ORGA);
    //                     else
    //                         $users=UtilisateurEntity::getAll(new SQLFactory());
    //                     $template->setLoop("userList",array_reduce($users,function($c,$e) use(&$i){
    //                         if($e->ID_USER!=$this->user->ID_USER){
    //                             $c[$i]=$e->getArray();
    //                         $c[$i]["ID_USER"]=strval($e->ID_USER);
    //                         $c[$i]["VALID_USER"]=strval($e->VALID_USER);
    //                         $c[$i]["USER_TYPE"]=strval($e->getRole(new SQLFactory())->LIBELLE_Type_Utilisateur);
    //                         $i++;
    //                         }
    //                         return $c;
    //                     },[]));
    //                     $template->setLoop("Addtype_utilisateur",array_reduce(TypeUtilisateur::getAll(new SQLFactory()),function($c,$e) use(&$i){
    //                         $c[$i]=$e->getArray();
    //                         $c[$i]["idType_Utilisateur"]=strval($e->idType_Utilisateur);
    //                         $i++;
    //                         return $c;
    //                     },[]));
    //                     $template->getRessourceManager()->addDirectJs("$(function(){\n
    //                         $('[name=addData]').click(function(){\n
    //                             addDataLine();
    //                             this.remove();
    //                             return false;
    //                     });});");
    //                     break;
    //                 }

    //         }
        
    //         $template->setVariables($this->getData());
    //         // Rendre le template
    //         $template->render();
    // }
}