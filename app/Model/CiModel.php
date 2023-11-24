<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of CiModel
 *
 * @author Martin Patyk
 */
final class CiModel extends BaseNDbModel
{
    use FetchPairsTrait;

    /** @var string nazev tabulky */
    protected $tableName = 'ci';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return array id, zazev
     */
    public static function fetchAllPairsWithCompanyName(): array
    {
        $r = dibi::select('[ci].[id]')
            ->select('[ci].[nazev]')
            ->select('[firma].[nazev]')->as('nazevFirmy')
            ->from('%n', 'ci')
            ->leftJoin('[firma]')->on('([firma].[id] = [ci].[firma])')
            ->orderBy('[firma].[nazev]')
            ->fetchAssoc('nazevFirmy,id');

        foreach ($r as $k => $v) {
            foreach ($v as $key => $value) {
                $r[$k][$key] = $value['nazev'];
            }
        }
        return $r;
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
