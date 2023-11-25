<?php

namespace App\Model;

use DateTime;
use Exception;
use Nette\Database\Context;
use Nette\Database\IRow;
use Nette\Utils\ArrayHash;
use Nette\InvalidArgumentException;
use Nette\NotImplementedException;


/**
 * Description of FakturaModel
 *
 * @author Martin Patyk
 */
final class FakturaModel extends BaseNDbModel
{
    public const TABLE_NAME = 'faktura';
    public const INVOICE_VS_LEN = 6;

    private $incidentModel;

    public function __construct(Context $context, IncidentModel $incidentModel)
    {
        parent::__construct(self::TABLE_NAME, $context);
        $this->incidentModel = $incidentModel;
    }


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
     * @return IRow|bool
     */
    public function fetchWithName(int $id)
    {
        return $this->explorer->table(self::TABLE_NAME)
            ->where("faktura.forma_uhrady = forma_uhrady.id")
            ->where("faktura.id", $id)
            ->select("faktura.*")
            ->select('CONCAT("Faktura: ",faktura.vs) AS title')
            ->select('(SELECT SUM(cena * pocet_polozek * koeficient_cena) FROM faktura_polozka WHERE faktura = ?) AS celkova_cena_bez_dph_bez_slevy', $id)
            ->select('(SELECT SUM(cena * pocet_polozek * koeficient_cena * (1-(sleva*0.01))) FROM faktura_polozka WHERE faktura = ?) AS celkova_cena_bez_dph', $id)
            ->select('forma_uhrady.nazev AS forma_uhrady')
            ->fetch();
    }

    /**
     * <b>!!! Nepouziva se. Bude to funkce pro rucni vkladani faktury !!!</b>
     *
     * Funkce vklada novou fakturu vcetne polozek
     */
    public function insert(ArrayHash $newItem)
    {
        throw new NotImplementedException("Nepouziva se. Bude to funkce pro rucni vkladani faktury");

        $this->explorer->beginTransaction();
        try {
            //polozky si dam bokem
            $polozky = $newItem['polozky'];
            //odeberu polozky z formulare
            $newItem->offsetUnset('polozky');
            dump($newItem);
            dump($polozky);
            exit;
            $this->explorer->table(self::TABLE_NAME)
                ->insert($newItem);
            // nactu si ID prave vlozene faktury
            $idFaktura = $this->getLastId();
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
            $this->explorer->rollBack();
            exit;
            #dump($tikety->fetchAll());
            //nastavim id prave vytvorene faktury incidentum, aby bylo zrejme ze uz
            //byly zauctovany
            $this->explorer->table(IncidentModel::TABLE_NAME)
                ->where('id', $incidentIds)
                ->update(['faktura' => $idFaktura]);

            $this->explorer->table(FakturaPolozkaModel::TABLE_NAME)
                ->insert($faktura_polozka);
            $this->explorer->commit();
        } catch (Exception $exc) {
            $this->explorer->rollBack();
            throw new InvalidArgumentException($exc->getMessage());
        }
    }

    /**
     * Funkce slouzi pro vytvoreni nove faktury z uzavrenych tiketu
     * @param IRow $newItem Inicialy pro novou fakturu
     */
    public function insertFromTickets(IRow $newItem)
    {
        //nactu si vsechny uzavrene tikety pro konkretniho zakaznika

        $this->explorer->beginTransaction();
        try {
            //nactu si vsechny uzavrene tikety, ktere patri odberateli
            $im = $this->incidentModel->selectAllTicketsForInvoicingByIdCompany($newItem['id_odberatel']);
            //odebetu id odberatele, ktere neni potreba.
            $newItem->offsetUnset('id_odberatel');
            $newItem->offsetSet('vs', $this->nextInvoiceVsRandom());
            $newItem->offsetSet('pdf_soubor', "");
            //vlozim novou fakturu
            $this->explorer->table(self::TABLE_NAME)->insert($newItem);
            // nactu si ID prave vlozene faktury
            $idFaktura = $this->getLastId();
            /*
             * Idealni funkce pro Triger
             */
            $fakturaPolozky = array();
            // posbiram vsechny idecka tiketu pro potrebu sparovani jejich sparovani s fakturou
            $incidentIds = array();
            //pro hromadne vlozeni potrebuji naplnit strukturu
            foreach ($im as $nadpis => $items) {
                $fakturaPolozky[] = [
                    "faktura" => $idFaktura,
                    "cssclass" => 2,
                    "nazev" => $nadpis,
                    "dodatek" => "",
                    "dph" => null,
                    "jednotka" => null,
                    "koeficient_cena" => 0,
                    "pocet_polozek" => 0,
                    "cena" => 0,
                    "sleva" => 0,
                ];
                foreach ($items as $tiket) {
                    $incidentIds[] = $tiket['incident_id'];
                    $fakturaPolozky[] = [
                        "faktura" => $idFaktura,
                        "cssclass" => 1,
                        "nazev" => $tiket['polozka_nazev'],
                        "dodatek" => $tiket['dodatek'],
                        "dph" => $tiket['dph'],
                        "jednotka" => $tiket['jednotka'],
                        "koeficient_cena" => $tiket['koeficient_cena'],
                        "pocet_polozek" => $tiket['pocet_polozek'],
                        "cena" => $tiket['cena_za_jednotku'],
                        "sleva" => 0,
                    ];
                }
            }
            //nastavim id prave vytvorene faktury incidentum, aby bylo zrejme ze uz
            //byly zauctovany
            $this->explorer->table(IncidentModel::TABLE_NAME)
                ->where('id', $incidentIds)
                ->update(['faktura' => $idFaktura]);
            //vlozim polozky faktury do databaze
//            dump($im);
//            dump($fakturaPolozky);
//            exit();
            $this->explorer->table(FakturaPolozkaModel::TABLE_NAME)->insert($fakturaPolozky);
            $this->explorer->commit();
        } catch (Exception $exc) {
            $this->explorer->rollBack();
            throw new InvalidArgumentException($exc->getMessage());
        }
    }

    /**
     * @return string
     */
    private function nextInvoiceVsRandom(): string
    {
        $today = new DateTime('now');
        $year = $today->format('Y');
        $randomMaxValue = str_pad('9', self::INVOICE_VS_LEN, '9', STR_PAD_LEFT);
        return $year . str_pad(rand(100, (int)$randomMaxValue), self::INVOICE_VS_LEN, "0", STR_PAD_LEFT);
    }

    /**
     * @param int $id
     */
    public function remove(int $id)
    {
        $this->explorer->beginTransaction();

        //test jestli faktura existuje
        if ($this->fetch($id)) {
            //tikety, ktere jsou zapocitane do faktury uvolnim
            $this->explorer->table(IncidentModel::TABLE_NAME)
                ->where('faktura', $id)
                ->update(['faktura' => null]);

            //smazu fakturu
            $this->explorer->table(self::TABLE_NAME)
                ->where('id', $id)
                ->delete();
            $this->explorer->commit();
        } else {
            $this->explorer->rollBack();
        }
    }
}
