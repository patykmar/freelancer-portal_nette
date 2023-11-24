<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of OdCiModel
 *
 * @author Martin Patyk
 */
final class OdCiModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $tableName = 'od_ci';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return string
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [od_ci] ORDER BY [nazev]');
    }

    /**
     * @throws DibiException
     */
    public static function fetchCiId($from)
    {
        return dibi::fetchSingle('SELECT [ci] FROM [od_ci] WHERE [od] = %s', $from, 'LIMIT 1');
    }
}