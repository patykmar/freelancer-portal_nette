<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of ZpusobUzavreniModel
 *
 * @author Martin Patyk
 */
final class ZpusobUzavreniModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $tableName = 'zpusob_uzavreni';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return string id, zazev
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [zpusob_uzavreni] ORDER BY [nazev]');
    }
}