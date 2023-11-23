<?php

namespace App\Model;

use dibi;
use DibiRow;
use Nette\NotImplementedException;

/**
 * Description of FrontaOsobaModel
 *
 * @author Martin Patyk
 */
final class FrontaOsobaModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'fronta_osoba';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return array id, zazev
     */
    public static function fetchPairs()
    {
        throw new NotImplementedException;
    }

    /**
     * Vrati v paru id a jmena pouze specialistu a systemovych uzivatelu.
     * @return DibiRow id => nazev
     * typ_osoby:
     *  1 - zakaznik
     *  2 - specialista
     *  3 - system
     */
    public static function fetchSpecialistPairsWithQueueName()
    {
        $r = dibi::select('[fronta_osoba].[id]')
            ->select('CONCAT([prijmeni]," ",[jmeno])')->as('osoba')
            ->select('[fronta].[nazev]')->as('fronta')
            ->from('%n', 'fronta_osoba')
            ->leftJoin('[fronta]')->on('([fronta].[id] = [fronta_osoba].[fronta])')
            ->leftJoin('[osoba]')->on('([osoba].[id] = [fronta_osoba].[osoba])')
            ->fetchAssoc('fronta,id');

        foreach ($r as $k => $v):
            foreach ($v as $key => $value):
                $r[$k][$key] = $value['osoba'];
            endforeach;
        endforeach;

        return $r;
    }

    public static function fetchAllWithOsobaAndFrontaName()
    {
        return dibi::select('[fronta_osoba].[id]')
            ->select('CONCAT([osoba].[jmeno]," ",[osoba].[prijmeni])')->as('[jmeno]')
            ->select('[fronta].[nazev]')->as('[fronta_nazev]')
            ->from('%n', 'fronta_osoba')
            ->leftJoin('[fronta]')->on('([fronta].[id] = [fronta_osoba].[fronta])')
            ->leftJoin('[osoba]')->on('([osoba].[id] = [fronta_osoba].[osoba])')
            ->fetchAssoc('id');
    }
}