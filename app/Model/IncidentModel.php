<?php

namespace App\Model;

use Exception;
use Nette\Database\Connection;
use Nette\Database\Context;
use Nette\Database\Table\IRow;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Nette\InvalidArgumentException;
use Nette\Utils\Strings;
use Tracy\Debugger;

/**
 * Description of IncidentModel
 *
 * @author Martin Patyk
 */
final class IncidentModel extends BaseModel
{
    public const TABLE_NAME = 'incident';
    private const INCIDENT_STAV_UZAVREN = 5;
    private const INCIDENT_STAV_CEKAM_NA_VYJADRENI_ZAKAZNIKA = 6;
    private const TYP_OSOBY_SYSTEM = 3;
    private const OSOBA_SS = 9;

    private $connection;
    private $incidentStavModel;
    private $incidentLogModel;
    private $typIncidentModel;
    private $prioritaModel;
    private $frontaOsobaModel;

    public function __construct(
        Context           $context,
        Connection        $connection,
        IncidentStavModel $incidentStavModel,
        IncidentLogModel  $incidentLogModel,
        TypIncidentModel  $typIncidentModel,
        PrioritaModel     $prioritaModel,
        FrontaOsobaModel  $frontaOsobaModel
    )
    {
        parent::__construct(self::TABLE_NAME, $context);
        $this->connection = $connection;
        $this->incidentStavModel = $incidentStavModel;
        $this->incidentLogModel = $incidentLogModel;
        $this->typIncidentModel = $typIncidentModel;
        $this->prioritaModel = $prioritaModel;
        $this->frontaOsobaModel = $frontaOsobaModel;
    }

    /**
     * Funkce nacita hodnoty do tiketu v textove podobe, aby se mohly informace
     * zobrazit uzivateli.
     * @param int $id cislo teketu
     */
    public function fetchWith3thPartyTable(int $id)
    {
        $query =
            'SELECT CONCAT(typ_incident.zkratka,incident.id) AS idTxt, ' .
            'incident.typ_incident, priorita, incident_stav, ukon, incident.ci, ' .
            'fronta_osoba, datum_ukonceni, datum_reakce, zpusob_uzavreni, incident.obsah, ' .
            'obsah_uzavreni, ovlivneni, incident.osoba_vytvoril, maly_popis, ' .
            'firma.nazev AS firma_nazev, ' .
            'fronta.nazev AS fronta, ' .
            'incident.datum_vytvoreni AS datum_vytvoreni, ' .
            'CONCAT(osoba.jmeno," ",osoba.prijmeni) AS osoba_vytvoril_text, ' .
            '(SELECT count(id) FROM  incident WHERE incident = ?) AS pocetPotomku ' .
            'FROM ' . self::TABLE_NAME . ' ' .
            'LEFT JOIN osoba ON incident.osoba_vytvoril = osoba.id ' .
            'LEFT JOIN ci ON incident.ci = ci.id ' .
            'LEFT JOIN firma ON ci.firma = firma.id ' .
            'LEFT JOIN typ_incident ON typ_incident.id = incident.typ_incident ' .
            'LEFT JOIN fronta_osoba ON incident.fronta_osoba = fronta_osoba.id ' .
            'LEFT JOIN fronta ON fronta_osoba.fronta = fronta.id ' .
            'WHERE incident.id = ? ';
        $result = $this->connection->query($query, $id, $id)->fetch();

        if ($result):
            return $result;
        endif;
        throw new InvalidArgumentException('Tiket cislo ' . $id . ' nebyl nalezen');
    }

    public function retrieveListOfUnpaidWork(): array
    {
        $query = "SELECT firma.nazev, firma.id AS firma_id, " .
            "count(incident.id) AS pocet_incidentu, " .
            "sum(tarif.cena * sla.cena_koeficient * zpusob_uzavreni.koeficient_cena) AS cena_nevyuctovano " .
            'FROM ' . self::TABLE_NAME . ' ' .
            "LEFT JOIN ci ON incident.ci = ci.id " .
            "LEFT JOIN firma ON ci.firma = firma.id " .
            "LEFT JOIN tarif ON ci.tarif = tarif.id " .
            "LEFT JOIN sla ON ci.tarif = sla.priorita = incident.priorita " .
            "AND sla.typ_incident = incident.typ_incident " .
            "AND sla.tarif = ci.tarif " .
            "LEFT JOIN zpusob_uzavreni ON incident.zpusob_uzavreni = zpusob_uzavreni.id " .
            "WHERE incident_stav = 5 AND faktura is null " .
            "GROUP BY ci.firma";
        $result = $this->connection->query($query)->fetchAll();

        if ($result):
            return $result;
        endif;
        throw new InvalidArgumentException('Zadna odvedena prace nebyla nalezena');
    }

    /**
     * Funkce vrati vsechny tikety, ktere jsou filtrovane podle stavu tiketu.
     * K tiketum se nactou i identifikatory front.
     * @param int $id identifikator stavu tiketu
     * @return IRow[]
     * id    nazev
     * 1    Otevřen
     * 2    Přiřazen
     * 3    Probíhá realizace
     * 4    Vyřešeno
     * 5    Uzavřeno
     * 6    Čeká se na vyjádření zákazníka
     * 7    Znovu otevřen
     */
    public function fetchAllIdByStav(int $id = 1): array
    {
        return $this->explorer->table(self::TABLE_NAME)
            ->where("incident.ci = ci.id")
            ->where("incident_stav", $id)
            ->select('incident.id')
            ->select('datum_uzavreni')
            ->select('ci.fronta_tier_1')
            ->select('ci.fronta_tier_2')
            ->select('ci.fronta_tier_3')
            ->fetchAll();
    }

    /**
     * select incident.id, incident.maly_popis, OS1.jmeno, OS1.prijmeni, OS2.jmeno, OS2.prijmeni
     * from incident
     * inner join fronta_osoba AS FO1 ON incident.fronta_osoba = FO1.id
     * inner join osoba AS OS1 on FO1.osoba = OS1.id
     * left join fronta_osoba AS FO2 ON FO1.fronta = FO2.fronta
     * left join osoba AS OS2 on FO2.osoba = OS2.id
     * where incident.incident_stav = 2
     * AND OS1.typ_osoby = 3 -- system
     * AND OS2.typ_osoby not in (1,3);
     *
     * Funkce nacte a vrati vsechny neprirazene tikety na frontach a
     * tikety kteri jsou na SSacich
     * @return array|object|\stdClass|null key => value
     */
    public function fetchAllSsTickets()
    {
        $query = "SELECT " .
            "incident.id AS ticket, FO1.id AS ss, FO2.id AS specialista " .
            "FROM " . self::TABLE_NAME . " " .
            "INNER JOIN fronta_osoba AS FO1 ON incident.fronta_osoba = FO1.id " .
            "INNER JOIN osoba AS OS1 ON FO1.osoba = OS1.id " .
            "LEFT JOIN fronta_osoba AS FO2 ON FO1.fronta = FO2.fronta " .
            "LEFT JOIN osoba AS OS2 ON FO2.osoba = OS2.id " .
            "WHERE incident.incident_stav = 2 " .
            "AND OS1.typ_osoby = 3 " . // nactu si systemove uzivatele ; SS
            "AND OS2.typ_osoby = 2 ";
        return $this->connection->query($query)->fetchAssoc('tiket|specialista');
    }

    /**
     * Nacte detail incidentu pro klienta. Jelikoz klient nema moznost evidovat
     * nektere hodnoty je potrebu mu nacist s nazvy polozek.
     * @return bool|\Nette\Database\IRow
     */
    public function fetchKlientEditIncident(int $id)
    {
        $query = 'SELECT ' .
            'incident.typ_incident, priorita, incident.obsah, datum_ukonceni, datum_reakce, zpusob_uzavreni, ' .
            'obsah_uzavreni, CONCAT(osoba_prirazen.jmeno," ",osoba_prirazen.prijmeni) AS osoba_prirazen ' .
            'CONCAT(osoba_vytvoril.jmeno," ",osoba_vytvoril.prijmeni) AS osoba_vytvoril ' .
            'CONCAT(typ_incident.zkratka,incident.id) AS idTxt ' .
            'incident.datum_vytvoreni AS datum_vytvoreni, incident_stav.nazev AS datum_vytvoreni, ' .
            'incident_stav.nazev AS incident_stav, fronta_prirazen.nazev AS fronta, ' .
            'firma.nazev AS firma_nazev, ci.nazev AS ci, ci.firma, osoba_vytvoril.firma ' .
            'FROM ' . self::TABLE_NAME . ' ' .
            'LEFT JOIN typ_incident ON incident.typ_incident = typ_incident.id ' .
            'LEFT JOIN priorita ON incident.priorita = priorita.id ' .
            'LEFT JOIN incident_stav ON incident.incident_stav = incident_stav.id ' .
            'LEFT JOIN ci ON incident.ci = ci.id ' .
            'LEFT JOIN firma ON ci.firma = firma.id ' .
            'LEFT JOIN fronta_osoba ON incident.fronta_osoba = fronta_osoba.id ' .
            'LEFT JOIN osoba AS osoba_prirazen ON fronta_osoba.osoba = osoba_prirazen.id ' .
            'LEFT JOIN fronta AS fronta_prirazen ON fronta_osoba.fronta = fronta_prirazen.id ' .
            'LEFT JOIN osoba AS osoba_vytvoril ON incident.osoba_vytvoril = osoba_vytvoril.id ' .
            'WHERE incident.id = ? AND  ci.firma = osoba_vytvoril.firma';

        return $this->connection->query($query, $id)->fetch();
    }

    /**
     * Funkce nacte tiket, u ktereho je mozne zaslat feedback. Funkce slouzi pro
     * overeni. Pokud neco najde v databazi tak vrati radek z databaze dle ID.
     * @param int $id Description
     * @return bool|IRow
     * @throws InvalidArgumentException
     */
    public function fetchForFeedBack(int $id)
    {
        $result = $this->explorer->table(self::TABLE_NAME)
            ->where("id", $id)
            ->where("odezva_cekam", true)
            ->where("odezva_odeslan_pozadavek", true)
            ->fetch();
        if ($this->checkNullOrFalse($result)) {
            throw new InvalidArgumentException('Tiket nebyl nalezen');
        }
        return $result;
    }

    /**
     * Nactu si tikety, ktere byli pro daneho odberatele uzavrene.
     * @param int $firmaId ID firma odberatel
     */
    public function selectAllTicketsForInvoicingByIdCompany(int $firmaId)
    {
        $query = 'SELECT ' .
            'CONCAT("Produkt: ",ci.nazev,", Způsob uzavření: ",zpusob_uzavreni.nazev) AS nadpis, ' .
            'CONCAT("Služba: ",ukon.nazev,", Priorita: ", priorita.nazev) AS dodatek, ' .
            'CONCAT(typ_incident.zkratka,incident.id," - ",maly_popis) AS polozka_nazev, ' .
            'ROUND((typ_incident.koeficient_cena * ovlivneni.koeficient_cena * priorita.koeficient_cena * zpusob_uzavreni.koeficient_cena),2) AS koeficient_cena, ' .
            'ci.nazev AS produkt, ' .
            'ukon.nazev AS ukon, ' .
            'zpusob_uzavreni.nazev AS uzavreno, ' .
            'priorita.nazev AS priorita, ' .
            'ukon.cena AS cena_za_jednotku, ' .
            'incident.id AS incident_id, ' .
            '1 AS pocet_polozek, ' .
            '1 AS dph, ' . // id DPH 0%
            '1 AS jednotka ' . // id jednotka neurcito 0%
            'FROM ' . self::TABLE_NAME . ' ' .
            'INNER JOIN ukon ON incident.ukon = ukon.id ' .
            'INNER JOIN ovlivneni ON incident.ovlivneni = ovlivneni.id ' .
            'INNER JOIN typ_incident ON incident.typ_incident = typ_incident.id ' .
            'INNER JOIN priorita ON incident.priorita = priorita.id ' .
            'INNER JOIN zpusob_uzavreni ON incident.zpusob_uzavreni = zpusob_uzavreni.id ' .
            'INNER JOIN ci ON incident.ci = ci.id ' .
            'INNER JOIN firma ON ci.firma = firma.id ' .
            'INNER JOIN tarif ON ci.tarif = tarif.id ' .
            'WHERE incident_stav = ? ' .
            'AND faktura is null ' .
            'AND firma.id = ? ' .
            'ORDER BY zpusob_uzavreni,priorita.nazev,typ_incident.nazev ';
        return $this->connection->query($query, self::INCIDENT_STAV_UZAVREN, $firmaId)
            ->fetchAssoc('nadpis|incident_id');
    }

    /**
     * Nactu si tikety, ktere byli pro daneho odberatele uzavrene.
     * @param int ID firma odberatel
     */
    public function selectAllTicketsForInvoicingByIdCompanyOld($id)
    {
        $query = 'SELECT ' .
            'CONCAT(typ_incident.zkratka, incident.id) AS idTxt ' .
            'CONCAT("Produkt: ",ci.nazev," - Uzavřeno: ", zpusob_uzavreni.nazev," - Priorita: ", priorita.nazev) AS nadpis ' .
            'CONCAT(typ_incident.zkratka,incident.id," - ",maly_popis) AS polozka_nazev ' .
            'sla.cena_koeficientAS slaKoeficient ' .
            'zpusob_uzavreni.koeficient_cena AS uzavreniKoeficient ' .
            'tarif.cena AS cenaTarif ' .
            '(tarif.cena * sla.cena_koeficient * zpusob_uzavreni.koeficient_cena) AS cenaZaJednotku ' .
            '1 AS dph ' .
            '1 AS jednotka ' .
            'ci.nazev AS nazevCi ' .
            'FROM ' . self::TABLE_NAME . ' ' .
            'LEFT JOIN typ_incident ON incident.typ_incident = typ_incident.id ' .
            'LEFT JOIN priorita ON incident.priorita = priorita.id ' .
            'LEFT JOIN zpusob_uzavreni ON incident.zpusob_uzavreni = zpusob_uzavreni.id ' .
            'LEFT JOIN ci ON incident.ci = ci.id ' .
            'LEFT JOIN firma ON ci.firma = firma.id ' .
            'LEFT JOIN tarif ON ci.tarif = tarif.id ' .
            'LEFT JOIN sla ON sla.priorita = incident.priorita ' .
            'AND sla.typ_incident = incident.typ_incident ' .
            'AND sla.tarif = ci.tarif ' .
            'WHERE incident_stav = 5 ' .
            'AND faktura is null ' .
            'AND firma.id = ? ' .
            'ORDER BY zpusob_uzavreni,priorita.nazev,typ_incident.nazev ';

        return $this->connection->query($query, $id)
            ->fetchAssoc('nazevCi|zpusob_uzavreni|priorita,id');
    }

    ###
    ###   VKLADANI
    ###

    /**
     * Vkladani noveho tiketu
     * @param ArrayHash $newItem form values
     */
    public function insertNewItem(ArrayHash $newItem): ArrayHash
    {
        $this->connection->beginTransaction();
        try {
            //   na zaklade vybrane priority a tarifu nastavenem na CI si vypocitam casy na reakci a dokonceni tiketu
            $queryCas = "SELECT " .
                "ADDDATE(ADDDATE(now(), INTERVAL + CONCAT(sla.reakce_mesic,' ',sla.reakce_hod,':',sla.reakce_min) MONTH), INTERVAL + sla.reakce_den DAY_MINUTE) AS reakce, " .
                "ADDDATE(ADDDATE(now(), INTERVAL + CONCAT(sla.hotovo_mesic,' ',sla.hotovo_hod,':',sla.hotovo_min) MONTH), INTERVAL + sla.hotovo_den DAY_MINUTE) AS hotovo, " .
                "FROM " . self::TABLE_NAME . " " .
                "LEFT JOIN sla ON sla.tarif = ci.tarif " .
                "AND sla.priorita = ? " .
                "AND sla.typ_incident = ? " .
                "AND ci.id = ? ";
            $cas = $this->connection->query($queryCas, $newItem['priorita'], $newItem['typ_incident'], $newItem['ci'])
                ->fetch();

            $newItem->offsetSet('datum_reakce', $cas['reakce']);
            $newItem->offsetSet('datum_ukonceni', $cas['hotovo']);

            //   nove tikety jsou odeslany automaticky na SD
            $newItem->offsetSet('fronta_osoba', 2);

            //   zapisu do datbaze
            $result = parent::insertNewItem($newItem);

            //   nactu si id od prave zapsaneho incidentu a vytvorim text pro WL
            $lastId = $this->connection->getInsertId();

            //   naplnim docasne pole ktere pouziju pro vlozeni do tabulky WL
            $workLogArray = new ArrayHash;
            $workLogArray->offsetSet('incident', $lastId);
            $workLogArray->offsetSet('datum_vytvoreni', new DateTime);
            $workLogArray->offsetSet('osoba', $newItem['osoba_vytvoril']);

            //   nactu si nazvy k vlozenym hodnotam, aby byly citelne pro cloveka
            $query = 'SELECT ' .
                'incident_stav.nazev AS incident_stav, priorita.nazev AS priorita, ' .
                'fronta.nazev AS fronta ' .
                'FROM ' . self::TABLE_NAME . ' ' .
                'LEFT JOIN incident_stav ON incident.incident_stav = incident_stav.id ' .
                'LEFT JOIN priorita ON incident.priorita = priorita.id ' .
                'LEFT JOIN fronta_osoba ON incident.fronta_osoba = fronta_osoba.id ' .
                'LEFT JOIN fronta ON fronta_osoba.fronta = fronta.id ' .
                'WHERE incident.id = ? ';

            $dbFetch = $this->connection->query($query, $lastId)->fetch();

            //   vytvorim si text, ktery se zapise do WL, char(10) - novy radek
            $obsah = '**Tiket vytvořen**' . chr(10);
            $obsah .= ' **Typ incidentu**: ' . $dbFetch['incident_stav'] . chr(10);
            $obsah .= ' **Priorita**: ' . $dbFetch['priorita'] . chr(10);
            $obsah .= ' **Fronta**: ' . $dbFetch['fronta'] . chr(10);
            $obsah .= ' **Maly popis**: ' . $newItem['maly_popis'] . chr(10);
            $obsah .= ' **Popis požadavku**: ' . chr(10);
            $obsah .= $newItem['obsah'];

            $workLogArray->offsetSet('obsah', $obsah);
            //   uvolnim pamet s docasnymi promennymi
            unset($obsah, $dbFetch);
            //   zapisi do WL informace o novem tiketu
            $this->incidentLogModel->insertNewItem($workLogArray);
            $this->connection->commit();
        } catch (Exception $exc) {
            $this->connection->rollBack();
            // zapisu chybu do logy
            Debugger::log($exc->getMessage());
            throw new InvalidArgumentException($exc->getMessage());
        }
        return $result;
    }

    ###
    ###   EDITOVANI
    ###

    /**
     * Funkce rozsiruje moznosti rodicovske funkce o:
     * - audit zmen parametru v tiketu a jejich zapis do logu.
     * - v pripade ze se zmeni priorita je potreba prepocitat SLAcka.
     * @param ArrayHash $arr nove hodnoty z formulare odeslane uzivatelem.
     * @param int $id Identifikator radku na kterem se provadi zmena
     * @return void
     * @throws InvalidArgumentException
     */
    public function updateItem(ArrayHash $arr, int $id): void
    {
        // nejprve si nactu stare hodnoty radku z databaze pro potreby porovnani
        $dbData = $this->fetchWith3thPartyTable($id);
        //   Docasna promenna do ktere se budou ukladat radky work logu
        $wl = array();
        try {
            /*
             * Pokud je formular v jinem stavu nez [probiha realizace] a novy stav
             * je nastaven na [probiha realizace] nastavim osobu prirazen na aktualne
             * prihlaseneho uzivatele.
             */
            /*          if (isset($arr['incident_stav']) && $dbData['incident_stav'] != 3 && $arr['incident_stav'] = 3):
              #$arr->offsetUnset('priorita');
              #$arr->offsetUnset('obsah');
              $arr->offsetSet('osoba_prirazen', $arr['identity']);
              endif; */

            /*
             * Pokud je nastavena promenna zpusob uzavreni prejdu
             * do stavu uzavren vyresen.
             */
            if (isset($arr['zpusob_uzavreni']) && $arr['zpusob_uzavreni']):
                $wlTemp = '**Incident byl uzavren s textem:** <br />';
                $wlTemp .= '<div class="wlCloseNote">' . $arr['obsah_uzavreni'] . '</div>';
                $wl[] = $wlTemp;
                unset($wlTemp);
                $arr->offsetSet('incident_stav', 4);
                $arr->offsetSet('osoba_uzavrel', $arr['identity']);
                $arr->offsetSet('datum_uzavreni', new DateTime);
                #$arr->offsetSet('osoba_prirazen', $arr['identity']);
                #$arr->offsetUnset('typ_incident');
                #$arr->offsetUnset('priorita');
            endif;

            /*
             * AUDIT
             * Prochazi se jednotlive polozky a porovnavaji se zmeny (hodnoty z formulare
             * oproti datum ulozenych v databazi).
             * Soubezne s tim probiha testovani zda je ve formulari nastavena
             * promenna, ktera je z pohledu auditu zajimava. Nektere polozky ve
             * vystupu z formularetam vybec nemuseji byt.
             */
            //   docasna promenna, pokud se nastavi na TRUE prepocita se cas reakce a dokonceni tiketu
            $novyCas = false;
            //
            //   TYP_INCIDENTU
            //
            if (isset($arr['typ_incident']) && $arr['typ_incident'] !== $dbData['typ_incident']) {
                $tmp = $this->typIncidentModel->fetchPairs();
                $new = $tmp[$arr['typ_incident']];

                // Je potreba osetrit nulovou hodnotu u stareho zaznamu jinak hrozi
                // nacteni prvku z pole, ktere neni definovane

                if ($dbData['typ_incident']) {
                    $old = $tmp[$dbData['typ_incident']];
                    $wl[] = '**Typ incidentu:** ' . $new . ' <span class="old">bylo: ' . $old . '</span>';
                } else {
                    $wl[] = '**Typ incidentu:** ' . $new;
                }
                //   prepocitej cas
                $novyCas = true;
                //   uvolnim z pameti docasne promenne
                unset($tmp, $old, $new);
            }
            //
            //   PRIORITA
            //
            if (isset($arr['priorita']) && $arr['priorita'] !== $dbData['priorita']) {
                $tmp = $this->prioritaModel->fetchPairs();
                $new = $tmp[$arr['priorita']];
                /*
                 * Je potreba osetrit nulovou hodnotu u stareho zaznamu jinak hrozi
                 * nacteni prvku z pole, ktere neni definovane
                 */
                if ($dbData['priorita']) {
                    $old = $tmp[$dbData['priorita']];
                    $wl[] = '**Priorita:** ' . $new . ' <span class="old">bylo: ' . $old . '</span>';
                } else {
                    $wl[] = '**Priorita:** ' . $new;
                }
                //   prepocitej cas
                $novyCas = true;
                //   uvolnim z pameti docasne promenne
                unset($tmp, $old, $new);
            }
            //
            //   STAV_INCIDENTU
            //
            if (isset($arr['incident_stav']) && $arr['incident_stav'] !== $dbData['incident_stav']) {
                $tmp = $this->incidentStavModel->fetchPairs();
                $new = $tmp[$arr['incident_stav']];
                /*
                 * Je potreba osetrit nulovou hodnotu u stareho zaznamu jinak hrozi
                 * nacteni prvku z pole, ktere neni definovane
                 */
                if ($dbData['incident_stav']) {
                    $old = $tmp[$dbData['incident_stav']];
                    $wl[] = '**Stav incidentu:** ' . $new . ' <span class="old">bylo: ' . $old . '</span>';
                } else {
                    $wl[] = '**Stav incidentu:** ' . $new;
                }
                //   uvolnim z pameti docasne promenne
                unset($tmp, $old, $new);
            }
            //
            //   Zmena OSOBY a fronty na ktere je tiket PRIRAZEN
            //

            /**
             * array (6)
             * 1 => DibiRow #253d
             * id => 1
             *            jmeno => "Martin Patyk" (12)
             *            fronta_nazev => "Programatori" (12)
             * 6 => DibiRow #0414
             * id => 6
             *            jmeno => "Shift Supervisor" (16)
             *            fronta_nazev => "Programatori" (12)
             * 2 => DibiRow #a33b
             * id => 2
             *            jmeno => "Service Desk" (12)
             *            fronta_nazev => "TIER 1" (6)
             */
            if (isset($arr['fronta_osoba']) && $arr['fronta_osoba'] !== $dbData['fronta_osoba']):
                //   asociativni pole s hodnotami obsazene v tabulce
                $tmp = $this->frontaOsobaModel->fetchAllWithOsobaAndFrontaName();
                #dump($tmp);
                #exit;
                $new = $tmp[$arr['fronta_osoba']];
                /*
                 * Je potreba osetrit nulovou hodnotu u stareho zaznamu jinak hrozi
                 * nacteni prvku z pole, ktere neni definovane
                 */
                if ($dbData['fronta_osoba']) {
                    $old = $tmp[$dbData['fronta_osoba']];
                    $wl[] = '**Přiřazeno:** ' . $new['jmeno'] . ' <span class="old">bylo: ' . $old['jmeno'] . '</span>';
                    $wl[] = '**Fronta:** ' . $new['fronta_nazev'] . ' <span class="old">bylo: ' . $old['fronta_nazev'] . '</span>';
                } else {
                    $wl[] = '**Přiřazeno:** ' . $new['jmeno'];
                    $wl[] = '**Fronta:** ' . $new['fronta_nazev'];
                }
                //   uvolnim z pameti docasne promenne
                unset($tmp, $old, $new, $modelOsoba);
            endif;

            //   pokud je $novyCas = TRUE pak prepocitej casy
            if ($novyCas) {
                //pri uprave priority je potreba prepocitat cas do kdy se ma tiket vytvorit
                $queryCas = 'SELECT ' .
                    'incident.datum_ukonceni AS stary_datum_ukonceni, ' .
                    'incident.datum_reakce AS stary_datum_reakce, ' .
                    "ADDDATE(ADDDATE(incident.datum_vytvoreni, INTERVAL + CONCAT(sla.reakce_mesic,' ',sla.reakce_hod,':',sla.reakce_min) MONTH), INTERVAL + sla.reakce_den DAY_MINUTE) AS nove_datum_ukonceni, " .
                    "ADDDATE(ADDDATE(incident.datum_vytvoreni, INTERVAL + CONCAT(sla.hotovo_mesic,' ',sla.hotovo_hod,':',sla.hotovo_min) MONTH), INTERVAL + sla.hotovo_den DAY_MINUTE) AS nove_datum_reakce, " .
                    'FROM ' . self::TABLE_NAME . ' ' .
                    "LEFT JOIN ci ON incident.ci = ci.id " .
                    "LEFT JOIN sla ON ci.tarif = sla.tarif " .
                    "LEFT JOIN sla ON ci.tarif = sla.tarif " .
                    "AND sla.typ_incident = ? " .
                    "AND sla.priorita = ? " .
                    "WHERE incident.id = ?";

                $cas = $this->connection->query($queryCas, $arr['incident_stav'], $arr['priorita'], $id)->fetch();

                $wl[] = '**Datum dokončení:** ' .
                    $cas['nove_datum_ukonceni'] .
                    ' <span class="old">bylo: ' .
                    $cas['stary_datum_ukonceni'] .
                    '</span>';
                $wl[] = '**Datum reakce:** ' .
                    $cas['nove_datum_reakce'] .
                    ' <span class="old">bylo: ' .
                    $cas['stary_datum_reakce'] .
                    '</span>';

                // nastavim nove casy reakce a dokonceni tiketu
                $arr->offsetSet('datum_ukonceni', $cas['nove_datum_ukonceni']);
                $arr->offsetSet('datum_reakce', $cas['nove_datum_reakce']);
            }

            /*
             * Zjistim jestli bylo neco zapsano v policku WL uzivatelem. Pokud ano
             * zapisu do pridam tento text do docasneho pole $wl. Jedna se o vstup
             * od uzivatele je potreba odstranim prebytecne mezery.
             */
            if (!empty($arr['wl'])) {
                $wl[] = Strings::trim($arr['wl']);
            }

            //   pokud ma WL alespon jeden zaznam tak zapis do WL hodnoty
            if (!empty($wl)) {
                /**    @var ArrayHash Description */
                $item = new ArrayHash();
                $item->offsetSet('incident', $id);
                $item->offsetSet('datum_vytvoreni', new DateTime);
                $wlTmp = '';
                foreach ($wl as $var) {
                    $wlTmp .= $var . '<br />';
                }
                $item->offsetSet('obsah', $wlTmp);
                $item->offsetSet('osoba', $arr['identity']);
                $this->incidentLogModel->insert($item);
            }
            /*
             * Pred odeslanim hodnot do databaze odeberu polozky, ktere se
             * nacitaji z jinych tabulek
             */
            $arr->offsetUnset('wl');
            $arr->offsetUnset('idTxt');
            #$arr->offsetUnset('fronta');
            $arr->offsetUnset('firma_nazev');
            $arr->offsetUnset('ci');
            #$arr->offsetUnset('osoba_vytvoril');
            $arr->offsetUnset('datum_vytvoreni');
            $arr->offsetUnset('datum_ukonceni');
            $arr->offsetUnset('datum_reakce');
            $arr->offsetUnset('identity');

            parent::updateItem($arr, $id);
        } catch (Exception $exc) {
            throw new InvalidArgumentException($exc->getMessage());
        }
    }

    /**
     * @return array
     */
    public function retrieveAllTicketWhichAreInStateOpenAndReOpened(): array
    {
        $query = "ci.fronta_tier_2, fronta_osoba.id AS fronta_osoba_id " .
            "FROM " . self::TABLE_NAME . " " .
            "WHERE incident_stav IN (1, 7) " .
            "INNER JOIN ci ON incident.ci = ci.id " .
            "LEFT JOIN fronta_osoba ON fronta_osoba.fronta = ci.fronta_tier_2 " .
            "AND fronta_osoba.osoba = ?";

        return $this->connection->query($query, self::OSOBA_SS)->fetchAll();
    }

    /**
     * @return array
     */
    public function closeAllTicketAfter14DaysWithNoFeedBack(): array
    {
        return $this->explorer->table(self::TABLE_NAME)
            ->where('incident.osoba_vytvoril = osoba.id')
            ->where('incident_stav', self::INCIDENT_STAV_CEKAM_NA_VYJADRENI_ZAKAZNIKA)
            ->where('DATEDIFF(now(),datum_uzavreni) > ?', 5)
            ->fetchAll();
    }

    /**
     * @return array
     */
    public function retrieveAllTicketForWaitingFeedback(): array
    {
        return $this->explorer->table(self::TABLE_NAME)
            ->where('incident.osoba_vytvoril = osoba.id')
            ->where('incident_stav', self::INCIDENT_STAV_CEKAM_NA_VYJADRENI_ZAKAZNIKA)
            ->where('typ_osoby', self::TYP_OSOBY_SYSTEM)
            ->fetchAll();
    }

    /**
     * @return array|IRow[]
     */
    public function retrieveAllSubTickets(): array
    {
        return $this->explorer->table(self::TABLE_NAME)
            ->where('incident_stav', self::INCIDENT_STAV_CEKAM_NA_VYJADRENI_ZAKAZNIKA)
            ->where('incident IS NOT null')
            ->fetchAll();
    }
}
