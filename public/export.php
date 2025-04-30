<?php
use SQLEntities\MessageEntity;
use SQLEntities\MessageTbl;
use SQLEntities\UtilisateurEntity;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Master\SQLFactory;
require_once ("vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

use vendor\easyFrameWork\Core\Master\Router;

use vendor\easyFrameWork\Core\Master\Autoloader;

//use Core\Master\Controller\HomeController;
EasyFrameWork::INIT();
if(isset($_GET["data"])){
    $sessionManager=EasyGlobal::createSessionManager();
    $current=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UtilisateurEntity");
    $display="";
    switch($_GET["data"]){
        case "user":{
            $users=[];
            if($current->ID_ORGA!=null){
                $users=array_reduce(UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(),"ID_ORGA",$current->ID_ORGA),function($c,$e){
                    $c[]=$e->getArray();
                    return $c;
                },[]);
            }
            $content[] = "ID#PSEUDO#MAIL#ARRIVE#VALIDITE#TYPE";
            foreach($users as $u){
                $e=[$u["ID_USER"],$u["PSEUDO_USER"],$u["MAIL_USER"],$u["ARRIVE_UTILISATEUR"],$u["DATE_VALIDITE"],$u["TYPE_UTILISATEUR"]];
                $content[] = implode("#", $e);
            }
            $display = implode("\n", $content);
            $d = date('Y-m-d');
            $filename = "tmp/export_user-$d";
            break;
        }
    }
    
}
if(isset($_GET["getMessage"])){
    $m=MessageEntity::getMessageTblBy(new SQLFactory(),"ID_SUJET",$_GET["id"]);
    if($m){
        $i=0;
        $return =(gettype($m)!="array")?$m->getArray():array_reduce($m,function($c,$e) use(&$i){
            $c[$i]=$e->getArray();
           $u=$e->getUser(new SQLFactory());
            $c[$i]["PSEUDO_USER"]=$u->PSEUDO_USER;
            $i++;
            return $c;
        },[]);
        $content[] = "ID#Date#Contenu#Utilisateur#displayed";
        $i=0;
        foreach($return as $q){
            $e=[$q["ID_MESSAGE"],$q["DATE_MESSAGE"],html_entity_decode($q["CONTENT_MESSAGE"]),$q["PSEUDO_USER"],$q["DISPLAY_"]];
            $content[] = implode("#", $e);
        }
        $display = implode("\n", $content);
 
    
    }else{
        $display="Pas de message";
    }
    $d = date('Y-m-d');
    $filename = "tmp/export_AllMessages-$d";
   
}
file_put_contents("$filename.csv", $display);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename("$filename.csv") . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize("$filename.csv"));
readfile("$filename.csv");
unlink("$filename.csv");
exit();