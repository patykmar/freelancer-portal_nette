<?php

namespace App\Model;

use dibi;

/**
 * Description of JednotkaModel
 *
 * @author Martin Patyk
 */
final class JednotkaModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $tableName = 'jednotka';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return array id, zazev
     */
    public static function fetchPairs()
    {
        return dibi::select('[id]')
            ->select('CONCAT([nazev]," ",[zkratka])')
            ->from('[jednotka]')
            ->fetchPairs();
    }
}
