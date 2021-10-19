<?php

namespace App\Model;

use dibi;

/**
 * Description of TypIncidentModel
 *
 * @author Martin Patyk
 */
final class TypIncidentModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'typ_incident';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni
     * cizich klicu ve formulari
     * @return array id, zazev
     */
    public static function fetchPairs()
    {
        return dibi::select('id')
            ->select('nazev')
            ->from('typ_incident')
            ->orderBy('nazev')
            ->fetchPairs();
    }

    /**
     * Vrati vsechny hlavni typy tyketu bez jejich tasku
     * @param bool $rodice Ovlivnuje jestli se nactou potomci nebo rodicovske typy
     * @return array id, zazev
     */
    public static function fetchPairsMain($rodice = TRUE)
    {
        if ($rodice) {
            return dibi::select('id')
                ->select('nazev')
                ->from('typ_incident')
                ->orderBy('nazev')
                ->where('typ_incident')->is(Null)
                ->fetchPairs();
        } else {
            return dibi::select('id')
                ->select('nazev')
                ->from('typ_incident')
                ->orderBy('nazev')
                ->where('typ_incident')->isNot(Null)
                ->fetchPairs();
        }
    }
}