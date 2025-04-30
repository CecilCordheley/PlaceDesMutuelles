<?php
namespace vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\ResourceManager;
use vendor\easyFrameWork\Core\Master\EasyTemplate;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\UtilisateurTbl;
use SQLEntities\OrganismeEntity;
use DateTime;
class ContactController extends Controller{
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
    public static function renderAgendaWithSlots(OrganismeEntity $organisme, EasyTemplate &$template): void {
        // Définir la timezone
        date_default_timezone_set('Europe/Paris');
        
        // Récupérer les données de planning avec créneaux et occupés
        $planningDataWithSlots = $organisme->getPlanningDataWithSlots(new SQLFactory(), 10);
        $planningData = $planningDataWithSlots["planning"];
      //  EasyFrameWork::Debug($planningData);
        $occupedSlots = $planningDataWithSlots["occuped"];
        
        // Jours de la semaine
        $joursSemaine = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
        $joursDisponibles = [];
        $slotsParJour = [];
    
        // Date actuelle
        $currentDate = new DateTime();
    
        foreach ($joursSemaine as $index => $jour) {
            // Vérifiez si un planning existe pour ce jour spécifique
            if (!isset($planningData[$jour]) || empty($planningData[$jour]['slots'])) {
                continue; // Ignorer ce jour
            }
    
            // Calculer la date exacte du jour
            $currentDayIndex = (int)$currentDate->format('N') - 1; // Lundi = 0
            $dayOffset = $index - $currentDayIndex; // Offset en jours
            $specificDate = (clone $currentDate)->modify("+$dayOffset days");
            $formattedDate = $specificDate->format('Y-m-d');
    
            // Créneaux pour le jour actuel
            $daySlots = $planningData[$jour]['slots'];
    
            // Normaliser les créneaux occupés pour comparaison
            $normalizedOccupiedSlots = array_map(function ($time) {
                return date('H:i', strtotime($time));
            }, $occupedSlots[$jour] ?? []);
    
            // Récupérer les créneaux disponibles
            $slots = array_values(array_filter(array_map(function ($slot) use ($normalizedOccupiedSlots, $formattedDate) {
                $normalizedSlot = date('H:i', strtotime($slot));
                $isOccupied = in_array($normalizedSlot, $normalizedOccupiedSlots);
    // Calcul de l'heure de fin en ajoutant 5 minutes
$endTimeString = date('H:i', strtotime($formattedDate . ' ' . $normalizedSlot . ' +10 minutes'));
$slotTime = strtotime($formattedDate . ' ' . $normalizedSlot);
$actualTime = strtotime(date('Y-m-d H:i'));
                // Obtenir l'heure actuelle
                $isPast = strtotime($slotTime < $actualTime && $actualTime<strtotime($endTimeString));
    
                if (!$isPast) {
                    return [
                        "SLOT_TIME" => $normalizedSlot,
                        "DATE" => $formattedDate,
                        "STATE" => $isOccupied ? "occupied" : "free",
                    ];
                }
                return null;
            }, $daySlots)));
    
            // Ajouter aux jours disponibles si au moins un créneau est valide
            if (!empty($slots)) {
                $joursDisponibles[] = [
                    "JOUR" => $jour,
                    "DATE" => $formattedDate,
                ];
                $slotsParJour[$jour] = $slots;
            }
        }
    
        // Injecter les données dans le template
        $template->setLoop("joursDisponibles", $joursDisponibles);
        $template->setLoop("slotsParJour", $slotsParJour); // Peut être utilisé avec JavaScript pour charger les créneaux
    }
    

    
    public function handleRequest(){
        $sessionManager=EasyGlobal::createSessionManager();
        $user=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
        $config=parse_ini_file("include/config.ini",true)["localhost"];
        $template = new EasyTemplate($config,new ResourceManager());
        $template->addScript("_js/Prototyper.js");
        $template->addScript("_js/exchange.js");
        $template->addScript("_js/alert.js");
        $template->remplaceTemplate("MainContent","Contact.tpl");
        $template->addStylesheet("_css/exchange.css");
        $template->addStylesheet("_css/alert.css");
        $time=[
            "now"=>date("H:i:s")
        ];
        $this->setData("Time",$time);
        $sqlF=new SQLFactory();
        $orga=$user->getOrganisme($sqlF);
      //  EasyFrameWork::Debug($orga);
      //  EasyFrameWork::Debug($orga->getPlanningDataWithSlots($sqlF,5));
    self::renderAgendaWithSlots($orga,$template);
      $exchange=$user->getExchange($sqlF);
     
      //EasyFrameWork::Debug($nextE);
      if(gettype($exchange)=="array"){
        $nextE=AdminController::formatExchangeList($exchange);
     // EasyFrameWork::Debug($LastExchange);
      if(count($nextE)>0){
        $template->setLoop("exchange",$nextE);
        
        $this->setData("exchange",current($nextE));
      }else{
        $template->cancelLoop("exchange","");
        $this->setData("exchange","0");
      }
    }else{
        if(gettype($exchange)!="bool"){
            //EasyFrameWork::Debug($exchange->getArray());
            if($exchange!="")
                $this->setData("exchange",$exchange->getArray());
        }else
            $this->setData("exchange","0");
    }
     // EasyFrameWork::Debug($LastExchange);
        //ICI LE CODE

        $template->setVariables($this->getData());
        // Rendre le template
        $template->render();
    }
}