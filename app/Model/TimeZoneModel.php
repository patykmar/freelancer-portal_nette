<?php

namespace App\Model;

/**
 * Description of TimeZoneModel
 *
 * @author Martin Patyk
 */
final class TimeZoneModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'time_zone';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return array
     */
    public function fetchPairs(): array
    {
        return $this->explorer->table($this->name)->order('nazev')->fetchPairs('id', 'nazev');
    }
}
