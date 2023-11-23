<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of StavCiModel
 *
 * @author Martin Patyk
 */
final class StavCiModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'stav_ci';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return string
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [stav_ci] ORDER BY [nazev]');
    }
}