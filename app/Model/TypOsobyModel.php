<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of TypOsobyModel
 *
 * @author Martin Patyk
 */
final class TypOsobyModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'typ_osoby';


    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return string
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [typ_osoby] ORDER BY [nazev]');
    }
}