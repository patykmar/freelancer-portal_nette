<?php

namespace App\Model;

use dibi;
use DibiException;
use Nette\ArrayHash;
use Nette\InvalidArgumentException;
use Nette\NotImplementedException;


/**
 * Description of FakturaModel
 *
 * @author Martin Patyk
 */
final class FakturaModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'faktura';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return void id, zazev
     */
    public static function fetchPairs()
    {
        throw new NotImplementedException();
    }

    /**
     * Funkce nacitajici data pro vygenerovani fakturty
     * @param int $id identifikator faktury
     */
    public function fetchWithName($id)
    {
        return dibi::select('[faktura].[*]')
            ->select('CONCAT("Faktura: ",[faktura].[vs])')->as('title')
            #->select('sum([dph].[koeficient] * [cena] * [pocet_polozek])')->as('celkova_cena_s_dph')
            ->select('(SELECT SUM([cena] * [pocet_polozek] * [koeficient_cena]) FROM [faktura_polozka] WHERE [faktura] = %i)', $id)->as('celkova_cena_bez_dph_bez_slevy')
            ->select('(SELECT SUM([cena] * [pocet_polozek] * [koeficient_cena] * (1-(sleva*0.01))) FROM [faktura_polozka] WHERE [faktura] = %i)', $id)->as('celkova_cena_bez_dph')
            ->select('forma_uhrady.nazev')->as('forma_uhrady')
            ->from('%n', $this->name)
            ->leftJoin('[forma_uhrady]')->on('([faktura].[forma_uhrady] = [forma_uhrady].[id])')
            ->where('[faktura].[id] = %i', $id)
            ->fetch();
    }

    /**
     * <b>!!! Nepouziva se. Bude to funkce pro rucni vkladani faktury !!!</b>
     *
     * Funkce vklada novou fakturu vcetne polozek
     * @throws DibiException
     */
    public function insert(ArrayHash $newItem)
    {
        throw new NotImplementedException("Nepouziva se. Bude to funkce pro rucni vkladani faktury");

        dibi::begin();
        try {
            //	polozky si dam bokem
            $polozky = $newItem['polozky'];
            //	odeberu polozky z formulare
            $newItem->offsetUnset('polozky');
            dump($newItem);
            dump($polozky);
            exit;
            dibi::insert('faktura', $newItem)
                ->execute();
            // nactu si ID prave vlozene faktury
            $idFaktura = dibi::insertId();
            $novePolozky = array(
                'nazev' => array(),
                'pocet_polozek' => array(),
                'jednotka' => array(),
                'dph' => array(),
                'cena' => array(),
            );
            foreach ($polozky as $item):
                if ($item['nazev'] != ''):
                    $novePolozky['nazev'][] = $item['nazev'];
                    $novePolozky['pocet_polozek'][] = $item['pocet_polozek'];
                    $novePolozky['jednotka'][] = $item['jednotka'];
                    $novePolozky['dph'][] = $item['dph'];
                    $novePolozky['cena'][] = $item['cena'];
                else:
                    continue;
                endif;
            endforeach;
            dibi::rollback();
            exit;
            #dump($tikety->fetchAll());
            //	nastavim id prave vytvorene faktury incidentum, aby bylo zrejme ze uz
            //	byly zauctovany
            dibi::update('incident', array('faktura' => $idFaktura))
                ->where('id')->in($incidentIds)
                ->execute();
            dibi::query('INSERT INTO [faktura_polozka] %m', $faktura_polozka);
            dibi::commit();
        } catch (DibiException $exc) {
            dibi::rollback();
            throw new InvalidArgumentException($exc->getMessage());
        }
    }

    /**
     * Funkce slouzi pro vytvoreni nove faktury z uzavrenych tiketu
     * @param DibiRow $newItem Inicialy pro novou fakturu
     * @throws DibiException
     */
    public function insertFromTickets(DibiRow $newItem)
    {
        //	nactu si vsechny uzavrene tikety pro konkretniho zakaznika

        dibi::begin();
        try {
            //	nactu si vsechny uzavrene tikety pro konkretniho zakaznika
            $incidentModel = new IncidentModel;
            //	nactu si vsechny uzavrene tikety, ktere patri odberateli
            $im = $incidentModel->selectAllTicketsForInvoicingByIdCompany($newItem['id_odberatel']);
            #dump($im->fetchAssoc('nadpis,id'));
            #exit;
            //	odebetu id odberatele, ktere neni potreba.
            $newItem->offsetUnset('id_odberatel');
            //	vlozim novou fakturu
            dibi::insert('faktura', $newItem)
                ->execute();
            // nactu si ID prave vlozene faktury
            $idFaktura = dibi::insertId();
            /*
             * Idealni funkce pro Triger
             */
            // vlozim VS k fakture, pouzije se aktualni rok a ID prave vlozene faktury
            dibi::query('UPDATE %n ', $this->name, ' ',
                'SET vs = CONCAT(YEAR(NOW()),LPAD(faktura.id,6,"0")) ',
                'WHERE ID = %i', $idFaktura);
            //	docasne pole pro naplneni p
            $faktura_polozka = array(
                'cssclass' => array(),
                'nazev' => array(),
                'dodatek' => array(),
                'dph' => array(),
                'jednotka' => array(),
                'koeficient_cena' => array(),
                'pocet_polozek' => array(),
                'sleva' => array(),
                'cena' => array(),
                'faktura' => array(),
            );
            // posbiram vsechny idecka tiketu pro potrebu sparovani jejich sparovani s fakturou
            $incidentIds = array();

            //	pro hromadne vlozeni potrebuji naplnit strukturu
            foreach ($im->fetchAssoc('nadpis,id') as $nadpis => $items) {
                $faktura_polozka['cssclass'][] = 2;
                $faktura_polozka['nazev'][] = $nadpis;
                $faktura_polozka['dodatek'][] = NULL;
                $faktura_polozka['dph'][] = NULL;
                $faktura_polozka['jednotka'][] = NULL;
                $faktura_polozka['koeficient_cena'][] = 0;
                $faktura_polozka['pocet_polozek'][] = 0;
                $faktura_polozka['cena'][] = 0;
                $faktura_polozka['sleva'][] = 0;
                $faktura_polozka['faktura'][] = $idFaktura;
                foreach ($items as $tiket) {
                    $incidentIds[] = $tiket['id'];
                    $faktura_polozka['cssclass'][] = 1;
                    $faktura_polozka['nazev'][] = $tiket['polozka_nazev'];
                    $faktura_polozka['dodatek'][] = $tiket['dodatek'];
                    $faktura_polozka['dph'][] = $tiket['dph'];
                    $faktura_polozka['jednotka'][] = $tiket['jednotka'];
                    $faktura_polozka['koeficient_cena'][] = $tiket['koeficient_cena'];
                    $faktura_polozka['pocet_polozek'][] = $tiket['pocet_polozek'];
                    $faktura_polozka['cena'][] = $tiket['cena_za_jednotku'];
                    $faktura_polozka['sleva'][] = 0;
                    $faktura_polozka['faktura'][] = $idFaktura;
                }
            }
            //	nastavim id prave vytvorene faktury incidentum, aby bylo zrejme ze uz
            //	byly zauctovany

            dibi::update('incident', array('faktura' => $idFaktura))
                ->where('id')->in($incidentIds)
                ->execute();
            //	vlozim polozky faktury do databaze
            dibi::query('INSERT INTO [faktura_polozka] %m', $faktura_polozka);
            dibi::commit();
        } catch (DibiException $exc) {
            dibi::rollback();
            throw new InvalidArgumentException($exc->getMessage());
        }
    }

    /**
     * @param int $id
     * @throws DibiException
     */
    public function remove($id)
    {
        dibi::begin();
        //	test jestli faktura existuje
        if ($this->fetch($id)) {
            //	tikety, ktere jsou zapocitane do faktury uvolnim
            dibi::update('incident', array('faktura' => NULL))
                ->where('faktura = %i', $id)
                ->execute();

            //	smazu fakturu
            dibi::delete($this->name)
                ->where('id = %i', $id)
                ->limit(1)
                ->execute();
            dibi::commit();
        } else {
            dibi::rollback();
        }
    }
}