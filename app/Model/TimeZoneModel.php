<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of TimeZoneModel
 *
 * @author Martin Patyk
 */
final class TimeZoneModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'time_zone';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return string
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [time_zone] ORDER BY [nazev]');
    }
}