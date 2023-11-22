<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of IncidentStavModel
 *
 * @author Martin Patyk
 */
final class IncidentStavModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'incident_stav';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return string
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [incident_stav]');
    }
}