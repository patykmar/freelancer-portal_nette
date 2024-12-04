<?php

/**
 * Virtualni Service Desk prostredi
 *
 * @author Martin Patyk
 */

namespace App\CronModule\Presenters;

use App\Components\SmtpController\SendMailService;
use App\Model\IncidentModel;
use Nette\Application\AbortException;
use Nette\Application\UI\InvalidLinkException;
use Nette\Utils\ArrayHash;

class Sd24hPresenter extends CronBasePresenter
{
    private const int IDENTITY_SD24 = 8;
    private const int INCIDENT_STAV_UZAVREN = 5;
    private const int INCIDENT_STAV_CEKAM_NA_VYJADRENI_ZAKAZNIKA = 6;
    private IncidentModel $incidentModel;
    private SendMailService $sendMailService;

    public function __construct(IncidentModel $incidentModel, SendMailService $sendMailService)
    {
        parent::__construct();
        $this->incidentModel = $incidentModel;
        $this->sendMailService = $sendMailService;
    }

    /**
     * Funkce ve ktere se zaviraji tikety podle ruznych scenaru
     * @throws AbortException
     */
    public function actionZaviraniTiketu(): void
    {
        $this->zavriTiketyVytvoreneSystemem();
        $this->zavriUkoloveTikety();
        $this->zavriTiketyStarsi14Dni();

        //aktualne neposilej maily
        //$this->odesliVyzvyKeZpetneVazbe();

        // hotovo dvacet vybavene :)
        $this->terminate();
    }

    /**
     * Nactu tikety ktere vytvoril SD a CD. Tyto tikety se nemaji kam poslat.
     */
    public function zavriTiketyVytvoreneSystemem()
    {
        $tickets = $this->incidentModel->retrieveAllTicketForWaitingFeedback();

        foreach ($tickets as $item) {
            $update = new ArrayHash;
            $update->offsetSet('incident_stav', self::INCIDENT_STAV_UZAVREN);  // nastaven stav uzavreno
            $update->offsetSet('identity', self::IDENTITY_SD24); // identita uzivattele
            $update->offsetSet('odezva_cekam', null); // neumoznim odeslat feedback
            $this->incidentModel->updateItem($update, $item['id']);
            unset($update);
        }
        unset($tickets);
    }

    /**
     * Nactu si tikety ktere jsou vice jak 14 dni bez zpetne vazby. Tyto tikety
     * zavru bez naroku na zpetnou vazbu.
     */
    public function zavriTiketyStarsi14Dni()
    {
        $tickets = $this->incidentModel->closeAllTicketAfter14DaysWithNoFeedBack();

        foreach ($tickets as $item) {
            $update = new ArrayHash;
            $update->offsetSet('incident_stav', self::INCIDENT_STAV_UZAVREN);  // nastaven stav uzavreno
            $update->offsetSet('identity', self::IDENTITY_SD24); // identita uzivattele
            $update->offsetSet('odezva_cekam', false); // neumoznim odeslat feedback
            $this->incidentModel->updateItem($update, $item['id']);
            unset($update);
        }
        unset($tickets);
    }

    /**
     * K ukolum neni potreba posilat zpetnou vazbu
     */
    public function zavriUkoloveTikety(): void
    {
        /*
         * do budoucna muze byt problem s tim ze to bude zavirat i incidenty pricleneny
         * k problemu, nebo change az se dodela change a problem management
         */
        $subTickets = $this->incidentModel->retrieveAllSubTickets();

        foreach ($subTickets as $item) {
            $update = new ArrayHash;
            $update->offsetSet('incident_stav', self::INCIDENT_STAV_UZAVREN);
            $update->offsetSet('identity', self::IDENTITY_SD24);
            $update->offsetSet('odezva_cekam', false); // neumoznim odeslat feedback
            $this->incidentModel->updateItem($update, $item['id']);
            unset($update);
        }
    }

    /**
     * Nactu si tikety ktere cekaji na zpetnou vazbu a pokud nebyl mail jeste
     * odeslan tak cloveku ktery vytvoril tiket posli mail s moznosti se vyjadrit.
     * U techto tiketu zmenim polozku odezva_odeslan_pozadavek na true.
     * @throws InvalidLinkException
     */
    public function odesliVyzvyKeZpetneVazbe()
    {
        $model = $this->incidentModel->getSelection();
        $model->select('CONCAT(typ_incident.zkratka,incident.id) AS idTxt')
            ->select('maly_popis, incident.obsah, obsah_uzavreni')
            ->select('incident.datum_ukonceni, incident.datum_vytvoreni')
            ->select('firma.nazev AS firma')
            ->select('ci.nazev AS ci')
            ->select('zpusob_uzavreni.nazev AS zpusob_uzavreni')
            ->select('osoba.email AS email')  // nactu mail od uzivatele
            ->select('osoba.typ_osoby AS typ_osoby')
//            ->innerJoin('typ_incident ON incident.typ_incident = typ_incident.id')
//            ->innerJoin('osoba ON incident.osoba_vytvoril = osoba.id')
//            ->innerJoin('zpusob_uzavreni')->on('incident.zpusob_uzavreni = zpusob_uzavreni.id')
//            ->innerJoin('ci')->on('incident.ci = ci.id')
//            ->innerJoin('firma')->on('ci.firma = firma.id')
            ->where('incident_stav = ?', self::INCIDENT_STAV_CEKAM_NA_VYJADRENI_ZAKAZNIKA)  // tikety ve stavu cekajici na vyjadreni
            ->where('odezva_cekam = ?', true) // tikety kde se ceka na odezvu
            ->where(['odezva_odeslan_pozadavek' => null]) // tikety kde se nebyla jeste odeslan pozadavek na feedback
            ->where(['incident' => null]) // tikety ktere nemaji predka
            ->where('typ_osoby NOT IN (3)');  // maily neposilam na systemove emaily


        // zapnu absolutni URL z duvodu potreby vegenerovani URL do mailu
        $this->absoluteUrls = true;
        foreach ($model->fetchAll() as $value) {

            // vlozim URL k odkazum na vyjadreni zpetne vazby
            $value->offsetSet('positiveLink', $this->link(':Front:FeedBack:Positive', $value['id']));
            $value->offsetSet('negativeLink', $this->link(':Front:FeedBack:Negative', $value['id']));

            // odeslu mail
            $this->sendMailService->odesliPozadavekNaZpetnoutVazbu($value);

            // nastavim hodnotu v DB ze byl odeslan pozadavek
            $change = new ArrayHash;
            $change->offsetSet('odezva_odeslan_pozadavek', true);
            $this->incidentModel->updateItem($change, $value['id']);

            unset($change);
        }

        // vypnu absolutni URL
        $this->absoluteUrls = false;

//        dump($Model->fetchAll());
//        dump($Model->fetchAssoc('email,id'));
//        $mail->odesliPozadavekNaZpetnoutVazbu($Model->fetchAssoc('email,id'));
    }
}
