<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\OrganismeTbl;
use SQLEntities\PlanningEntity;
use SQLEntities\EventEntity;
use vendor\easyFrameWork\Core\Main;
use Exception;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

/**
* Class personnalisée pour la table `Organisme`.
* Hérite de `OrganismeTbl`. Ajoutez ici vos propres méthodes.
*/
class OrganismeEntity extends OrganismeTbl
{
   // Ajoutez vos méthodes ici
   private function generateTimeSlots($startTime, $endTime, $interval) {
    $slots = [];
    $start = strtotime($startTime);
    $end = strtotime($endTime);

    while ($start < $end) {
        $slots[] = date('H:i', $start);
        $start += $interval * 60; // Convertir les minutes en secondes
    }

    return $slots;
}
public function getEvents($sqlF){
  return EventEntity::getEventTblBy($sqlF,"ID_ORGA",$this->ID_ORGA);
}
public function getExchange($sqlF){
  return ExchangeEntity::getExchangeTblBy($sqlF,"Organisme",$this->ID_ORGA);
}
public function getPlanningDataWithSlots(SQLFactory $sqlF, int $interval = 5) {
  $plannings = PlanningEntity::getplanningTblBy($sqlF, "organisme_tbl_ID_ORGA", $this->ID_ORGA);
  
  // Récupérer les échanges déjà planifiés
  $exchanges = ExchangeEntity::getExchangeTblBy($sqlF, "Organisme", $this->ID_ORGA);
 
  $occuped = [
      "Lundi" => [],
      "Mardi" => [],
      "Mercredi" => [],
      "Jeudi" => [],
      "Vendredi" => [],
      "Samedi" => [],
      "Dimanche" => []
  ];
  $days = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];

  // Récupérer les créneaux occupés
  foreach ($exchanges as $exchange) {
      $date = date_create($exchange->dateExchange);
      $day = $days[date_format($date, 'N') - 1]; // Convertir le jour numérique en texte
      $occuped[$day][] = $exchange->HeureDebut;
  }

  // Préparer les données de planification
  $planningData = array_reduce($plannings, function ($c, $e) use ($days) {
      $data = $e->getArray();

      // Mapper jouer_semaine à l'index du jour
      $dayName = $data['jouer_semaine']; // Assurez-vous que cette colonne existe dans votre base
      $dayIndex = array_search($dayName, $days); // Trouver l'index du jour

      if ($dayIndex !== false) {
          $data['day_index'] = $dayIndex;
          $c[$dayName] = $data; // Utiliser le nom du jour comme clé
      }

      return $c;
  }, []);

  // Ajouter les créneaux horaires pour chaque jour
  foreach ($planningData as &$day) {
      $day['slots'] = $this->generateTimeSlots($day['heure_debut'], $day['heure_fin'], $interval);
  }

  // Associer les jours manquants avec des créneaux vides
  $finalPlanning = [];
  foreach ($days as $dayIndex => $dayName) {
      $finalPlanning[$dayName] = isset($planningData[$dayName])
          ? $planningData[$dayName]
          : [
              "day_index" => $dayIndex,
              "heure_debut" => null,
              "heure_fin" => null,
              "slots" => [] // Pas de créneaux disponibles
          ];
  }

  return ["planning" => $finalPlanning, "occuped" => $occuped];
}



   public static function getAll($sqlF){
    $arr=ExchangeTbl::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(OrganismeTbl::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\OrganismeEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    /**
     * Summary of getOrganismeTblBy
     * @param mixed $sqlF
     * @param mixed $key
     * @param mixed $value
     * @param mixed $filter
     * @return mixed
     */
    public static function getOrganismeTblBy($sqlF,$key,$value,$filter=null){
      $arr=OrganismeTbl::getOrganismeTblBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\OrganismeEntity");
        return $c;
      },[]);
    }else 
    return Main::fixObject($arr,"SQLEntities\OrganismeEntity");
    }else{
      return false;
    }
      }
 }