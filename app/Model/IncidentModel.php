<?php

namespace App\Model;

use dibi;
use DibiException;
use DibiRow;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Nette\InvalidArgumentException;
use Nette\Utils\Strings;

/**
 * Description of IncidentModel
 *
 * @author Martin Patyk
 */
final class IncidentModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $tableName = 'incident';

    ###
    ### Nacitani dat
    ###

    /**
     * Funkce nacita hodnoty do tiketu v textove podobe, aby se mohly informace
     * zobrazit uzivateli.
     * @param int $id cislo teketu
     */
    public function fetchWith3thPartyTable($id)
    {
        $r = dibi::select('CONCAT([typ_incident].[zkratka],[incident].[id])')->as('idTxt')
            ->select('incident.typ_incident')
            ->select('priorita')
            ->select('incident_stav')
            ->select('fronta_osoba')
            ->select('incident.obsah')
            ->select('datum_ukonceni')
            ->select('datum_reakce')
            ->select('zpusob_uzavreni')
            ->select('obsah_uzavreni')
            ->select('ukon')
            ->select('ovlivneni')
            ->select('[incident].[osoba_vytvoril]')
            ->select('maly_popis')
            ->select('firma.nazev')->as('firma_nazev')
            ->select('fronta.nazev')->as('fronta')
            ->select('incident.ci')
            ->select('[incident].[datum_vytvoreni]')->as('[datum_vytvoreni]')
            ->select('CONCAT(osoba.jmeno," ",osoba.prijmeni)')->as('osoba_vytvoril_text')
            ->select('(SELECT count([id]) FROM  [incident] WHERE incident = %i)', $id)->as('pocetPotomku')
            ->from('%n', $this->tableName)
            ->leftJoin('osoba')->on('([incident].[osoba_vytvoril] = [osoba].[id])')
            ->leftJoin('ci')->on('([incident].[ci] = [ci].[id])')
            ->leftJoin('firma')->on('([ci].[firma] = [firma].[id])')
            ->leftJoin('typ_incident')->on('([typ_incident].[id] = [incident].[typ_incident])')
            ->leftJoin('fronta_osoba')->on('([incident].[fronta_osoba] = [fronta_osoba].[id])')
            ->leftJoin('fronta')->on('([fronta_osoba].[fronta] = [fronta].[id])')
            ->where('[incident].[id] = %i', $id)
            ->fetch();
        if ($r):
            return $r;
        endif;
        throw new InvalidArgumentException('Tiket cislo ' . $id . ' nebyl nalezen');
    }

    /**
     * Funkce vrati vsechny tikety, ktere jsou filtrovane podle stavu tiketu.
     * K tiketum se nactou i identifikatory front.
     * @param int $id identifikator stavu tiketu
     * id    nazev
     * 1    Otevřen
     * 2    Přiřazen
     * 3    Probíhá realizace
     * 4    Vyřešeno
     * 5    Uzavřeno
     * 6    Čeká se na vyjádření zákazníka
     * 7    Znovu otevřen
     */
    public function fetchAllIdByStav($id = 1)
    {
        return dibi::select('[incident].[id],[datum_uzavreni]')
            ->select('[ci].[fronta_tier_1]')
            ->select('[ci].[fronta_tier_2]')
            ->select('[ci].[fronta_tier_3]')
            ->from('%n', $this->tableName)
            ->where('incident_stav = %i', $id)
            ->leftJoin('ci')->on('([incident].[ci] = [ci].[id])')
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
     * @return DibiRow key => value
     */
    public function fetchAllSsTickets()
    {
        return dibi::select('incident.id')->as('tiket')
            ->select('FO1.id')->as('ss')
            ->select('FO2.id')->as('specialista')
            ->from('%n', $this->tableName)
            ->innerJoin('fronta_osoba')->as('FO1')->on('incident.fronta_osoba = FO1.id')
            ->innerJoin('osoba')->as('OS1')->on('FO1.osoba = OS1.id')
            ->leftJoin('fronta_osoba')->as('FO2')->on('FO1.fronta = FO2.fronta')
            ->leftJoin('osoba')->as('OS2')->on('FO2.osoba = OS2.id')
            ->where('incident.incident_stav = %i', 2)
            ->and('OS1.typ_osoby = %i', 3) // nactu si systemove uzivatele ; SS
            ->and('OS2.typ_osoby = %i', 2) // nacti mi pouze specialisty
            ->fetchAssoc('tiket,specialista');
        #->fetchAll();
    }

    /**
     * Nacte detail incidentu pro klienta. Jelikoz klient nema moznost evidovat
     * nektere hodnoty je potrebu mu nacist s nazvy polozek.
     * @return DibiRow key => value
     */
    public function fetchKlientEditIncident($id)
    {
        return dibi::select('incident.typ_incident')
            ->select('priorita')
            ->select('incident.obsah')
            ->select('datum_ukonceni')
            ->select('datum_reakce')
            ->select('zpusob_uzavreni')
            ->select('obsah_uzavreni')
            #->select('CONCAT([fronta_osoba].[osoba].[prijmeni],",",[fronta_osoba].[osoba].[jmeno])')->as('osoba_prirazen')
            ->select('CONCAT([osoba_prirazen].[jmeno]," ",[osoba_prirazen].[prijmeni])')->as('osoba_prirazen')
            ->select('CONCAT([osoba_vytvoril].[jmeno]," ",[osoba_vytvoril].[prijmeni])')->as('osoba_vytvoril')
            ->select('CONCAT([typ_incident].[zkratka],[incident].[id])')->as('idTxt')
            ->select('incident.datum_vytvoreni')->as('datum_vytvoreni')
            ->select('incident_stav.nazev')->as('incident_stav')
            ->select('fronta_prirazen.nazev')->as('fronta')
            ->select('firma.nazev')->as('firma_nazev')
            ->select('ci.nazev')->as('ci')
            ->select('ci.firma')
            ->select('osoba_vytvoril.firma')
            ->from('%n', $this->tableName)
            ->leftJoin('typ_incident')->on('incident.typ_incident = typ_incident.id')
            ->leftJoin('priorita')->on('incident.priorita = priorita.id')
            ->leftJoin('incident_stav')->on('incident.incident_stav = incident_stav.id')
#              ->leftJoin('fronta')->on('incident.fronta = fronta.id')
            ->leftJoin('ci')->on('incident.ci = ci.id')
            ->leftJoin('firma')->on('ci.firma = firma.id')
            ->leftJoin('fronta_osoba')->on('[incident].[fronta_osoba] = [fronta_osoba].[id]')
            ->leftJoin('osoba')->as('osoba_prirazen')->on('[fronta_osoba].[osoba] = [osoba_prirazen].[id]')
            ->leftJoin('fronta')->as('fronta_prirazen')->on('[fronta_osoba].[fronta] = [fronta_prirazen].[id]')
            ->leftJoin('osoba')->as('osoba_vytvoril')->on('incident.osoba_vytvoril = osoba_vytvoril.id')
            ->where('incident.id = %i', $id)->and('ci.firma = osoba_vytvoril.firma')
            ->fetch();
    }

    /**
     * Funkce nacte tiket, u ktereho je mozne zaslat feedback. Funkce slouzi pro
     * overeni. Pokud neco najde v databazi tak vrati radek z databaze dle ID.
     * @param int $id Description
     * @throws InvalidArgumentException
     */
    public function fetchForFeedBack($id)
    {
        $r = dibi::select('*')
            ->from('%n', $this->tableName)
            ->where('id = %i', $id)
            ->and('odezva_cekam = %b', TRUE)
            ->and('odezva_odeslan_pozadavek = %b', TRUE)
            ->fetch();
        if (!$r) {
            throw new InvalidArgumentException('Tiket nebyl nalezen');
        }
        return $r;
    }

    /**
     * Nactu si tikety, ktere byli pro daneho odberatele uzavrene.
     * @param int ID firma odberatel
     */
    public function selectAllTicketsForInvoicingByIdCompany($id)
    {
        return $this->fetchFactory()
            #->select('CONCAT("Produkt: ",[ci].[nazev]," ; Služba: ",[ukon].nazev)')->as('nadpis')
            #->select('CONCAT("Produkt: ",[ci].[nazev]," ; Služba: ",[ukon].nazev," ; Uzavřeno: ",[zpusob_uzavreni].[nazev]," ; Priorita: ", [priorita].[nazev])')->as('nadpis')
            ->select('CONCAT("Produkt: ",[ci].[nazev],", Způsob uzavření: ",[zpusob_uzavreni].[nazev])')->as('nadpis')
            ->select('CONCAT("Služba: ",[ukon].nazev,", Priorita: ", [priorita].[nazev])')->as('dodatek')
            ->select('[ci].[nazev]')->as('produkt')
            ->select('[ukon].[nazev]')->as('ukon')
            ->select('[zpusob_uzavreni].[nazev]')->as('uzavreno')
            ->select('1')->as('pocet_polozek')
            ->select('[priorita].[nazev]')->as('priorita')
            ->select('CONCAT([typ_incident].[zkratka],[incident].[id]," - ",[maly_popis])')->as('polozka_nazev')
            #->select('CONCAT("Uzavřeno: ", [zpusob_uzavreni].[nazev]," ; Priorita: ", [priorita].[nazev])')->as('dodatek')
            ->select('ROUND((typ_incident.koeficient_cena * ovlivneni.koeficient_cena * priorita.koeficient_cena * zpusob_uzavreni.koeficient_cena),2)')->as('koeficient_cena')
            ->select('ukon.cena')->as('cena_za_jednotku')
            ->select('1')->as('dph') // id DPH 0%

            ->select('1')->as('jednotka') // id jednotka neurcito 0%
            #->select('[ci].[nazev]')->as('nazevCi')
            ->innerJoin('ukon')->on('[incident].[ukon] = [ukon].[id]')
            ->innerJoin('ovlivneni')->on('[incident].[ovlivneni] = [ovlivneni].[id]')
            ->innerJoin('typ_incident')->on('([incident].[typ_incident] = [typ_incident].[id])')
            ->innerJoin('priorita')->on('([incident].[priorita] = [priorita].[id])')
            ->innerJoin('zpusob_uzavreni')->on('([incident].[zpusob_uzavreni] = [zpusob_uzavreni].[id])')
            ->innerJoin('ci')->on('([incident].[ci] = [ci].[id])')
            ->innerJoin('firma')->on('([ci].[firma] = [firma].[id])')
            ->innerJoin('tarif')->on('([ci].[tarif] = [tarif].[id])')
            ->where('incident_stav = %i', 5)
            ->and('faktura')->is(NULL)
            ->and('firma.id = %i', $id)
            ->orderBy('zpusob_uzavreni,priorita.nazev,typ_incident.nazev');
        //->fetchAssoc('nazevCi,zpusob_uzavreni,priorita,id');
    }

    /**
     * Nactu si tikety, ktere byli pro daneho odberatele uzavrene.
     * @param int ID firma odberatel
     */
    public function selectAllTicketsForInvoicingByIdCompanyOld($id)
    {
        return $this->fetchFactory()
            ->select('CONCAT([typ_incident].[zkratka], [incident].[id])')->as('idTxt')
            ->select('CONCAT("Produkt: ",[ci].[nazev]," - Uzavřeno: ", [zpusob_uzavreni].[nazev]," - Priorita: ", [priorita].[nazev])')->as('nadpis')
            #->select('[typ_incident].[nazev]')->as('typ_incident')
            #->select('[priorita].[nazev]')->as('priorita')
            #->select('[zpusob_uzavreni].[nazev]')->as('zpusob_uzavreni')
            ->select('CONCAT([typ_incident].[zkratka],[incident].[id]," - ",[maly_popis])')->as('polozka_nazev')
            ->select('[sla].[cena_koeficient]')->as('slaKoeficient')
            ->select('[zpusob_uzavreni].[koeficient_cena]')->as('uzavreniKoeficient')
            ->select('[tarif].[cena]')->as('cenaTarif')
            ->select('([tarif].[cena] * [sla].[cena_koeficient] * [zpusob_uzavreni].[koeficient_cena])')->as('cenaZaJednotku')
            ->select('1')->as('dph') // id DPH 0%
            ->select('1')->as('jednotka') // id jednotka neurcito 0%
            ->select('[ci].[nazev]')->as('nazevCi')
            ->leftJoin('typ_incident')->on('([incident].[typ_incident] = [typ_incident].[id])')
            ->leftJoin('priorita')->on('([incident].[priorita] = [priorita].[id])')
            ->leftJoin('zpusob_uzavreni')->on('([incident].[zpusob_uzavreni] = [zpusob_uzavreni].[id])')
            ->leftJoin('ci')->on('([incident].[ci] = [ci].[id])')
            ->leftJoin('firma')->on('([ci].[firma] = [firma].[id])')
            ->leftJoin('tarif')->on('([ci].[tarif] = [tarif].[id])')
            ->leftJoin('sla')->on('([sla].[priorita] = [incident].[priorita] AND [sla].[typ_incident] = [incident].[typ_incident] AND [sla].[tarif] = [ci].[tarif])')
            ->where('incident_stav = %i', 5)
            ->and('faktura')->is(NULL)
            ->and('firma.id = %i', $id)
            ->orderBy('zpusob_uzavreni,priorita.nazev,typ_incident.nazev');
        //->fetchAssoc('nazevCi,zpusob_uzavreni,priorita,id');
    }

    ###
    ###   VKLADANI
    ###

    /**
     * Vkladani noveho tiketu
     * @param ArrayHash $newItem form values
     * @throws InvalidArgumentException|DibiException
     */
    public function insert(ArrayHash $newItem)
    {
        dibi::begin();
        try {
            //   na zaklade vybrane priority a tarifu nastavenem na CI si vypocitam casy na reakci a dokonceni tiketu
            $cas = dibi::select("ADDDATE(ADDDATE(now(), INTERVAL + CONCAT(sla.reakce_mesic,' ',sla.reakce_hod,':',sla.reakce_min) MONTH), INTERVAL + sla.reakce_den DAY_MINUTE)")->as('[reakce]')
                ->select("ADDDATE(ADDDATE(now(), INTERVAL + CONCAT(sla.hotovo_mesic,' ',sla.hotovo_hod,':',sla.hotovo_min) MONTH), INTERVAL + sla.hotovo_den DAY_MINUTE)")->as('[hotovo]')
                ->from('%n', 'ci')
                ->leftJoin('sla')->on('[sla].[tarif] = [ci].[tarif]')
                ->and('[sla].[priorita] = %i', $newItem['priorita'])
                ->and('[sla].[typ_incident] = %i', $newItem['typ_incident'])
                ->where('[ci].[id] = %i', $newItem['ci'])
                ->fetch();

            $newItem->offsetSet('datum_reakce', $cas['reakce']);
            $newItem->offsetSet('datum_ukonceni', $cas['hotovo']);

            //   nove tikety jsou odeslany automaticky na SD
            $newItem->offsetSet('fronta_osoba', 2);

            //   zapisu do datbaze
            dibi::query('INSERT INTO %n', $this->tableName, ' %v', $newItem);

            //   nactu si id od prave zapsaneho incidentu a vytvorim text pro WL
            $lastId = $this->getLastId();

            //   naplnim docasne pole ktere pouziju pro vlozeni do tabulky WL
            $WlArr = new ArrayHash;
            $WlArr->offsetSet('incident', $lastId);
            $WlArr->offsetSet('datum_vytvoreni', new DateTime);
            $WlArr->offsetSet('osoba', $newItem['osoba_vytvoril']);

            //   nactu si nazvy k vlozenym hodnotam, aby byly citelne pro cloveka
            $dbFetch = dibi::select('[incident_stav].[nazev]')->as('[incident_stav]')
                ->select('[priorita].[nazev]')->as('[priorita]')
                ->select('[fronta].[nazev]')->as('[fronta]')
                ->from('%n', $this->tableName)
                ->leftJoin('[incident_stav]')->on('[incident].[incident_stav] = [incident_stav].[id]')
                ->leftJoin('[priorita]')->on('[incident].[priorita] = [priorita].[id]')
                ->leftJoin('[fronta_osoba]')->on('[incident].[fronta_osoba] = [fronta_osoba].[id]')
                ->leftJoin('[fronta]')->on('[fronta_osoba].[fronta] = [fronta].[id]')
                ->where('[incident].[id] = %i', $lastId)
                ->fetch();

            //   vytvorim si text, ktery se zapise do WL, char(10) - novy radek
            $obsah = '**Tiket vytvořen**' . chr(10);
            $obsah .= ' **Typ incidentu**: ' . $dbFetch['incident_stav'] . chr(10);
            $obsah .= ' **Priorita**: ' . $dbFetch['priorita'] . chr(10);
            $obsah .= ' **Fronta**: ' . $dbFetch['fronta'] . chr(10);
            $obsah .= ' **Maly popis**: ' . $newItem['maly_popis'] . chr(10);
            $obsah .= ' **Popis požadavku**: ' . chr(10);
            $obsah .= $newItem['obsah'];

            $WlArr->offsetSet('obsah', $obsah);
            //   uvolnim pamet s docasnymi promennymi
            unset($obsah, $dbFetch);
            //   zapisi do WL informace o novem tiketu
            $wlModel = new IncidentLogModel();
            $wlModel->insert($WlArr);
            dibi::commit();
        } catch (DibiException $exc) {
            dibi::rollback();
            // zapisu chybu do logy
            Debugger::log($exc->getMessage());
            throw new InvalidArgumentException($exc->getMessage());
        }
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
    public function update(ArrayHash $arr, $id)
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
            $novyCas = FALSE;
            //
            //   TYP_INCIDENTU
            //
            if (isset($arr['typ_incident']) && $arr['typ_incident'] !== $dbData['typ_incident']) {
                $tmp = TypIncidentModel::fetchPairs();
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
                $novyCas = TRUE;
                //   uvolnim z pameti docasne promenne
                unset($tmp, $old, $new);
            }
            //
            //   PRIORITA
            //
            if (isset($arr['priorita']) && $arr['priorita'] !== $dbData['priorita']) {
                $tmp = PrioritaModel::fetchPairs();
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
                $novyCas = TRUE;
                //   uvolnim z pameti docasne promenne
                unset($tmp, $old, $new);
            }
            //
            //   STAV_INCIDENTU
            //
            if (isset($arr['incident_stav']) && $arr['incident_stav'] !== $dbData['incident_stav']) {
                $tmp = IncidentStavModel::fetchPairs();
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
             *
             * 1 => DibiRow #253d
             *
             * id => 1
             *            jmeno => "Martin Patyk" (12)
             *            fronta_nazev => "Programatori" (12)
             *
             * 6 => DibiRow #0414
             *
             * id => 6
             *            jmeno => "Shift Supervisor" (16)
             *            fronta_nazev => "Programatori" (12)
             *
             * 2 => DibiRow #a33b
             *
             * id => 2
             *            jmeno => "Service Desk" (12)
             *            fronta_nazev => "TIER 1" (6)
             */
            if (isset($arr['fronta_osoba']) && $arr['fronta_osoba'] !== $dbData['fronta_osoba']):
                //   asociativni pole s hodnotami obsazene v tabulce
                $tmp = FrontaOsobaModel::fetchAllWithOsobaAndFrontaName();
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
                $cas = dibi::select('[incident].[datum_ukonceni]')->as('[stary_datum_ukonceni]')
                    ->select('[incident].[datum_reakce]')->as('[stary_datum_reakce]')
                    ->select("ADDDATE(ADDDATE(incident.datum_vytvoreni, INTERVAL + CONCAT(sla.reakce_mesic,' ',sla.reakce_hod,':',sla.reakce_min) MONTH), INTERVAL + sla.reakce_den DAY_MINUTE)")->as('[nove_datum_ukonceni]')
                    ->select("ADDDATE(ADDDATE(incident.datum_vytvoreni, INTERVAL + CONCAT(sla.hotovo_mesic,' ',sla.hotovo_hod,':',sla.hotovo_min) MONTH), INTERVAL + sla.hotovo_den DAY_MINUTE)")->as('[nove_datum_reakce]')
                    ->from('%n', $this->tableName)
                    ->leftJoin('ci')->on('[incident].[ci] = [ci].[id]')
                    ->leftJoin('sla')->on('[ci].[tarif] = [sla].[tarif]')
                    ->and('[sla].[typ_incident] = %i', $arr['incident_stav'])
                    ->and('[sla].[priorita] = %i', $arr['priorita'])
                    ->where('[incident].[id] = %i', $id)
                    ->fetch();
                $wl[] = '**Datum dokončení:** ' . $cas['nove_datum_ukonceni'] . ' <span class="old">bylo: ' . $cas['stary_datum_ukonceni'] . '</span>';
                $wl[] = '**Datum reakce:** ' . $cas['nove_datum_reakce'] . ' <span class="old">bylo: ' . $cas['stary_datum_reakce'] . '</span>';

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
            if (count($wl) > 0) {
                /**    @var ArrayHash Description */
                $item = new ArrayHash();
                $wlModel = new IncidentLogModel;
                $item->offsetSet('incident', $id);
                $item->offsetSet('datum_vytvoreni', new DateTime);
                $wlTmp = '';
                foreach ($wl as $var) {
                    $wlTmp .= $var . '<br />';
                }
                $item->offsetSet('obsah', $wlTmp);
                $item->offsetSet('osoba', $arr['identity']);
                $wlModel->insert($item);
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

            #dump($arr);
            #dump($wl);
            #exit;
            parent::update($arr, $id);
        } catch (DibiException $exc) {
            throw new InvalidArgumentException($exc->getMessage());
        }
    }
}
