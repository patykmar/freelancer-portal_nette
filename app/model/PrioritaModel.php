<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of PrioritaModel
 *
 * @author Martin Patyk
 */
final class PrioritaModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'priorita';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return string
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [priorita]');
    }
}