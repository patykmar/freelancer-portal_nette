<?php

namespace App\Model;

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
     * @return array
     */
    public function fetchPairs(): array
    {
        return $this->explorer->table($this->name)->order('nazev')->fetchPairs('id', 'nazev');
    }
}
