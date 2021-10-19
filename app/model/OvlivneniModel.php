<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of OvlivneniModel
 *
 * @author Martin Patyk
 */
final class OvlivneniModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'ovlivneni';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return string
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [ovlivneni] ORDER BY [nazev]');
    }
}