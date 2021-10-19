<?php

namespace App\Model;

use dibi;
use DibiRow;

/**
 * Description of OsobaModel
 *
 * @author Martin Patyk
 */
final class OsobaModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'osoba';

    /**
     * Vrati v paru id a jmena pouze specialistu a systemovych uzivatelu
     * @return DibiRow Description
     */
    public static function fetchPairsSpecialistSystem()
    {
        return self::fetchPairs();
    }

    /**
     * Vrati v paru id a jmena pouze specialistu a systemovych uzivatelu.
     * @return DibiRow id => nazev
     * typ_osoby:
     *  1 - zakaznik
     *  2 - specialista
     *  3 - system
     */
    public static function fetchPairs()
    {
        return dibi::select('id')
            ->select('CONCAT([jmeno]," ",[prijmeni])')->as('nazev')
            ->from('%n', 'osoba')
            ->where('typ_osoby')->in('(2,3)')
            ->orderBy('prijmeni')
            ->fetchPairs();
    }

    /**
     * Metoda vraci vsechny osoby k pouziti do formulare.
     */
    public static function fetchAllPairs()
    {
        return dibi::select('id')
            ->select('CONCAT([jmeno]," ",[prijmeni])')->as('nazev')
            ->from('%n', 'osoba')
            ->orderBy('prijmeni')
            ->fetchPairs();
    }

    /**
     * Metoda vraci vsechny osoby k pouziti do formulare. Jmena jsou rarazene
     * do firmy ve ktere se osoba nachazi.
     */
    public static function fetchAllPairsWithCompanyName()
    {
        $r = dibi::select('[osoba].[id]')
            ->select('CONCAT([prijmeni]," ",[jmeno])')->as('nazev')
            ->select('[firma].[nazev]')->as('nazevFirmy')
            ->from('%n', 'osoba')
            ->leftJoin('[firma]')->on('([firma].[id] = [osoba].[firma])')
            ->orderBy('[osoba].[prijmeni]')
            ->fetchAssoc('nazevFirmy,id');

        foreach ($r as $k => $v) {
            foreach ($v as $key => $value) {
                $r[$k][$key] = $value['nazev'];
            }
        }
        return $r;
    }
}