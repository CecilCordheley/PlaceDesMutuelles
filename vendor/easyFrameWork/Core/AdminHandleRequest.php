<?php
namespace EasyFrameWork\Core;

use SQLEntities\OrganismeEntity;
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
class RequestHandler {
    private $template;
    private $config;

    public function __construct($user) {
        $this->user = $user;
        $this->config = parse_ini_file("include/config.ini", true)["localhost"];
        $this->template = new EasyTemplate($this->config, new ResourceManager());
        $this->template->addStylesheet("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");
    }

    public function handleRequest() {
        if (!$this->checkUserAccess()) {
            return;
        }

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

            default:
                $this->template->renderAlert("Action inconnue.", "index.php");
                break;
        }

        $this->template->setVariables($this->getData());
        $this->template->render();
    }

    private function checkUserAccess() {
        if (!$this->user || $this->user->TYPE_UTILISATEUR == "1") {
            $this->template->addStylesheet("_css/alert.css");
            $this->template->renderAlert("Vous n'avez pas les droits nécessaires pour accéder à cette page", "index.php");
            return false;
        }
        return true;
    }

    private function handleExchange() {
        $droits = $this->user->getRights();

        if (!isset($droits["Exchange"])) {
            echo "Vous n'avez pas les droits nécessaires pour accéder à cette page";
            exit();
        }

        $this->activPage["EXCHANGE"] = " activ";
        $this->setData("activPage", $this->activPage);

        $orga = $this->user->getOrganisme(new SQLFactory());
        $exchange = $orga->getExchange(new SQLFactory());

        $this->template->remplaceTemplate("MainContent", "ADMIN/exchange.tpl");
        $this->template->setLoop("exchangeList", $this->formatExchangeList($exchange));
    }

    private function formatExchangeList($exchange) {
        $i = 0;
        return array_reduce($exchange, function ($c, $e) use (&$i) {
            if ($e->dateExchange == date("Y-m-d")) {
                $c[$i] = $e->getArray();
                $c[$i]["LOGIN_USER"] = $e->getArray()["UtilisateurEntity"]["PSEUDO_USER"];
                $i++;
            }
            return $c;
        }, []);
    }

    private function handleSujet() {
        if (!$this->checkAccess("Sujet")) {
            return;
        }

        $this->activPage["SUJET"] = " activ";
        $this->setData("activPage", $this->activPage);

        $templateData = $this->prepareSujetData();
        $this->template->setLoop("sujetList", $templateData["sujetList"]);
        $this->template->setLoop("Theme", $templateData["themeList"]);

        $this->template->remplaceTemplate("MainContent", "ADMIN/sujet.tpl");
    }

    private function prepareSujetData() {
        $sujet = SujetEntity::getAll(new SQLFactory());
        $sqlF = new SQLFactory();

        $sujetList = array_reduce($sujet, function ($c, $e) use ($sqlF) {
            $u = $e->getUser($sqlF);
            $data = $e->getArray();

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
        if (!$this->checkAccess("User")) {
            return;
        }

        $this->activPage["USER"] = " activ";
        $this->setData("activPage", $this->activPage);

        $users = $this->user->ID_ORGA
            ? UtilisateurEntity::getUtilisateurTblBy(new SQLFactory(), "ID_ORGA", $this->user->ID_ORGA)
            : UtilisateurEntity::getAll(new SQLFactory());

        $this->template->remplaceTemplate("MainContent", "ADMIN/user.tpl");
        $this->template->setLoop("userList", $this->formatUserList($users));
    }

    private function formatUserList($users) {
        $i = 0;
        return array_reduce($users, function ($c, $e) use (&$i) {
            if ($e->ID_USER != $this->user->ID_USER) {
                $c[$i] = $e->getArray();
                $c[$i]["ID_USER"] = strval($e->ID_USER);
                $c[$i]["VALID_USER"] = strval($e->VALID_USER);
                $c[$i]["USER_TYPE"] = $e->getRole(new SQLFactory())->LIBELLE_Type_Utilisateur;
                $i++;
            }
            return $c;
        }, []);
    }

    private function checkAccess($module) {
        $rights = $this->user->getRights($module);

        if (!$rights) {
            echo "Vous n'avez pas les droits nécessaires pour accéder à cette page";
            exit();
        }

        return true;
    }

    private function setData($key, $value) {
        $this->template->setVariable($key, $value);
    }

    private function getData() {
        return $this->template->getVariables();
    }
}
