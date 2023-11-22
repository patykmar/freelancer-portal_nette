<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of ZemeModel
 *
 * @author Martin Patyk
 */
final class ZemeModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'zeme';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return string 
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [zeme] ORDER BY [nazev]');
    }
}