<?php

namespace App\Model;

use dibi;
use DibiRow;

/**
 * Description of OsobaModel
 *
 * @author Martin Patyk
 */
final class OsobaModel extends BaseNDbModel
{
    /** @var string nazev tabulky */
    protected $tableName = 'osoba';

    /**
     * Vrati v paru id a jmena pouze specialistu a systemovych uzivatelu
     * @return array
     */
    public function fetchPairsSpecialistSystem()
    {
        return $this->fetchAll()->fetchPairs();
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
    public function fetchAllPairs()
    {
        $sql = "SELECT id, jmeno || ' ' || prijmeni AS nazev ";
        $sql .= "FROM osoba ";
        $sql .= "ORDER BY prijmeni";
        return $this->database->query($sql)->fetchPairs();
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