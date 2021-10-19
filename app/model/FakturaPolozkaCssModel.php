<?php

namespace App\Model;

use dibi;

/**
 * Description of FakturaPolozkaCssModel
 *
 * @author Martin Patyk
 */
final class FakturaPolozkaCssModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'faktura_polozka_css';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return array id, zazev
     */
    public static function fetchPairs()
    {
        return dibi::select('id')
            ->select('nazev')
            ->from('faktura_polozka_css')
            ->orderBy('nazev')
            ->fetchPairs();
    }
}