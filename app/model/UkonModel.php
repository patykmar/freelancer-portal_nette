<?php

namespace App\Model;

use dibi;

/**
 * Description of UkonModel
 *
 * @author Martin Patyk
 */
final class UkonModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'ukon';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return array id, zazev
     */
    public static function fetchPairs()
    {
        return dibi::select('[id]')
            ->select('[nazev]')
            ->from('[ukon]')
            ->orderBy('[nazev]')
            ->fetchPairs();
    }
}