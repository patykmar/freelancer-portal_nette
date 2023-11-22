<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of TypChangeModel
 *
 * @author Martin Patyk
 */
final class TypChangeModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'typ_change';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return array id, zazev
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [typ_change] ORDER BY [nazev]');
    }
}