<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of FrontaModel
 *
 * @author Martin Patyk
 */
final class FrontaModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'fronta';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return string
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [fronta] ORDER BY [nazev]');
    }
}