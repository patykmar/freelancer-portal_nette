<?php

namespace App\Model;

use dibi;
use DibiException;
use Nette\Database\Context;

/**
 * Description of CiModel
 *
 * @author Martin Patyk
 */
final class CiModel extends BaseNDbModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = "ci";

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }


    /**
     * Vrati Map<string, Map<int,string>>, kde klic je nazev firmy a v ni je mapa CiId => CiName
     * @return array
     */
    public function fetchAllPairsWithCompanyName(): array
    {
        $result = $this->explorer->table(self::TABLE_NAME)
            ->where("firma.id = ci.firma")
            ->order("firma.nazev")
            ->select("ci.id")
            ->select("ci.nazev")
            ->select("firma.nazev AS nazevFirmy")
            ->fetchAssoc("nazevFirmy|id");

        foreach ($result as $k => $v) {
            foreach ($v as $key => $value) {
                $result[$k][$key] = $value['nazev'];
            }
        }
        return $result;
    }

    /**
     * Funkce vklada novou polozku do tabulky CI a zaroven vytvori
     * zaznam v tabulce logu k CIcku
     * @throws DibiException
     */
//    public function insert(ArrayHash $newItem)
//    {
//        dibi::begin();
//        try {
//            //vytahnu si text logu do extra promenne a zrusim jej v poly
//            $log = $newItem['log'];
//            $newItem->offsetUnset('log');
//
//            //vlozim do databaze
//            dibi::query('INSERT INTO %n', $this->name, '%v', $newItem);
//            //nactu si idecko prave pridane polozky
//            $ci_id = dibi::getInsertId();
//
//            //pripravim si pole pro ulozeni logu do databaze
//            $ciLog = new ArrayHash;
//            $ciLog->offsetSet('ci', $ci_id);
//            $ciLog->offsetSet('datum_vytvoreni', new DateTime);
//            $ciLog->offsetSet('obsah', $log);
//
//            //vlozim novy zaznam do logu
//            $ciLogModel = new CiLogModel;
//            $ciLogModel->insert($ciLog);
//
//            //jestli je vse v poradku uloz do databaze
//            dibi::commit();
//        } catch (DibiException $exc) {
//            dibi::rollback();
//            // zapisu chybu do logy
//            Debugger::log($exc->getMessage());
//            throw new InvalidArgumentException($exc->getMessage());
//        }
//    }
}
