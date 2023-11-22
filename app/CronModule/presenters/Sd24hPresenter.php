<?php


/**
 * Virtualni Service Desk prostredi
 *
 * @author Martin Patyk
 */

namespace App\CronModule;

use greeny\MailLibrary;
use greeny\MailLibrary\Drivers\ImapDriver;
use Nette\ArrayHash;
use Nette\Database\Table\Selection;
use Nette\Utils\Strings;
use App\Model;

class Sd24hPresenter extends CronBasePresenter
{
    /** @var ImapDriver */
    private $driver;

    /** @var Selection databaze tasku k incidentu */
    private $modelIncident;

    /** @var int identifikator identiy uzivatele */
    private $identity;

    public function __construct()
    {
        parent::__construct();

        #$this->driver = new MailLibrary\Drivers\ImapDriver('webalerts@patyk.cz', 'Zu7tp0ic32vSeiUa8DUt', 'mail.patyk.cz', 143, FALSE);
        #$this->modelIncident = $this->context->database->context->table('incident');
        $this->modelIncident = new Model\IncidentModel;

        //	fronta-osoba, TIER1-SD24
        #$this->identity = 2;
        // SD24H
        $this->identity = 8;
    }


    /**
     * Funkce ve ktere se zaviraji tikety podle ruznych scenaru
     */
    public function actionZaviraniTiketu()
    {
        $this->zavriTiketyVytvoreneSystemem();
        $this->zavriUkoloveTikety();
        $this->zavriTiketyStarsi14Dni();

        //aktualne neposilej maily
        //$this->odesliVyzvyKeZpetneVazbe();

        //	hotovo dvacet vybavene :)
        $this->terminate();
    }

    /**
     * Nactu tikety ktere vytvoril SD a CD. Tyto tikety se nemaji kam poslat.
     */
    public function zavriTiketyVytvoreneSystemem()
    {
        $model = $this->modelIncident->fetchFactory();
        $model->leftJoin('osoba')->on('[incident].[osoba_vytvoril] = [osoba].[id]')
            ->where('incident_stav = %i', 6)    // cekam na vyjadreni zakaznika
            ->and('typ_osoby')->in('(3)'); // tikety co vytvoril system

        foreach ($model->fetchAll() as $item) {
            $update = new ArrayHash;
            $update->offsetSet('incident_stav', 5);  // nastaven stav uzavreno
            $update->offsetSet('identity', $this->identity); // identita uzivattele
            $update->offsetSet('odezva_cekam', NULL); // neumoznim odeslat feedback
            $this->modelIncident->update($update, $item['id']);
            unset($update);
        }
        unset($model);
    }

    /**
     * Nactu si tikety ktere jsou vice jak 14 dni bez zpetne vazby. Tyto tikety
     * zavru bez naroku na zpetnou vazbu.
     */
    public function zavriTiketyStarsi14Dni()
    {
        $model = $this->modelIncident->fetchFactory();
        $model->leftJoin('osoba')
            ->on('[incident].[osoba_vytvoril] = [osoba].[id]')
            ->where('incident_stav = %i', 6)    // cekam na vyjadreni zakaznika
            ->and('DATEDIFF(now(),datum_uzavreni) > %i', 5); //	tikety co jsou starsi 14 dni

        foreach ($model->fetchAll() as $item) {
            $update = new ArrayHash;
            $update->offsetSet('incident_stav', 5);  // nastaven stav uzavreno
            $update->offsetSet('identity', $this->identity); // identita uzivattele
            $update->offsetSet('odezva_cekam', FALSE); // neumoznim odeslat feedback
            $this->modelIncident->update($update, $item['id']);
            unset($update);
        }
        unset($model);
    }

    /**
     * K ukolum neni potreba posilat zpetnou vazbu
     */
    public function zavriUkoloveTikety()
    {
        $model = $this->modelIncident->fetchFactory();
        $model->where('incident_stav = %i', 6) // cekam na vyjadreni zakaznika
        /*
         * do budoucna muze byt problem s tim ze to bude zavirat i incidenty pricleneny
         * k problemu, nebo change az se dodela change a problem management
         */
        ->and('incident')->isNot(NULL); // tikety co maji predka -> ukoly

        foreach ($model->fetchAll() as $item) {
            $update = new ArrayHash;
            $update->offsetSet('incident_stav', 5);  // nastaven stav uzavreno
            $update->offsetSet('identity', $this->identity); // identita uzivattele
            $update->offsetSet('odezva_cekam', FALSE); // neumoznim odeslat feedback
            $this->modelIncident->update($update, $item['id']);
            unset($update);
        }
        unset($model);
    }


    /**
     * Nactu si tikety ktere cekaji na zpetnou vazbu a pokud nebyl mail jeste
     * odeslan tak cloveku ktery vytvoril tiket posli mail s moznosti se vyjadrit.
     * U techto tiketu zmenim polozku odezva_odeslan_pozadavek na true.
     */
    public function odesliVyzvyKeZpetneVazbe()
    {
        $mail = new \SendMail\SendMailControler();
        $model = $this->modelIncident->fetchFactory();
        $model->select('CONCAT([typ_incident].[zkratka],[incident].[id])')->as('idTxt')
            ->select('maly_popis, incident.obsah, obsah_uzavreni')
            ->select('incident.datum_ukonceni, incident.datum_vytvoreni')
            ->select('firma.nazev')->as('firma')
            ->select('ci.nazev')->as('ci')
            ->select('zpusob_uzavreni.nazev')->as('zpusob_uzavreni')
            ->select('osoba.email')->as('email')  // nactu mail od uzivatele
            ->select('osoba.typ_osoby')->as('typ_osoby')
            ->innerJoin('typ_incident')->on('[incident].[typ_incident] = [typ_incident].[id]')
            ->innerJoin('osoba')->on('[incident].[osoba_vytvoril] = [osoba].[id]')
            ->innerJoin('zpusob_uzavreni')->on('[incident].[zpusob_uzavreni] = [zpusob_uzavreni].[id]')
            ->innerJoin('ci')->on('[incident].[ci] = [ci].[id]')
            ->innerJoin('firma')->on('[ci].[firma] = [firma].[id]')
            ->where('incident_stav = %i', 6)  // tikety ve stavu cekajici na vyjadreni
            ->and('odezva_cekam = %b', TRUE) // tikety kde se ceka na odezvu
            ->and('odezva_odeslan_pozadavek')->is(NULL) // tikety kde se nebyla jeste odeslan pozadavek na feedback
            ->and('incident')->is(NULL) // tikety ktere nemaji predka
            ->and('typ_osoby')->notIn('(3)');  // maily neposilam na systemove emaily


        //	zapnu absolutni URL z duvodu potreby vegenerovani URL do mailu
        $this->absoluteUrls = TRUE;
        foreach ($model->fetchAll() as $value) {

            //	vlozim URL k odkazum na vyjadreni zpetne vazby
            $value->offsetSet('positiveLink', $this->link(':Front:FeedBack:Positive', $value['id']));
            $value->offsetSet('negativeLink', $this->link(':Front:FeedBack:Negative', $value['id']));

            // odeslu mail
            $mail->odesliPozadavekNaZpetnoutVazbu($value);

            // nastavim hodnotu v DB ze byl odeslan pozadavek
            $change = new ArrayHash;
            $change->offsetSet('odezva_odeslan_pozadavek', TRUE);
            $this->modelIncident->update($change, $value['id']);

            unset($change);
        }

        //	vypnu absolutni URL
        $this->absoluteUrls = FALSE;

//        dump($Model->fetchAll());
//        dump($Model->fetchAssoc('email,id'));
//        $mail->odesliPozadavekNaZpetnoutVazbu($Model->fetchAssoc('email,id'));
    }
}