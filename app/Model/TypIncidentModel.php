<?php

namespace App\Model;

/**
 * Description of TypIncidentModel
 *
 * @author Martin Patyk
 */
final class TypIncidentModel extends BaseNDbModel
{
    /** @var string */
    protected $tableName = 'typ_incident';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni
     * cizich klicu ve formulari
     * @return array id, zazev
     */
    public function fetchPairs(): array
    {
        return $this->fetchAll()
            ->order('nazev DESC')
            ->fetchPairs('id', 'nazev');
    }

    /**
     * Vrati vsechny hlavni typy tyketu bez jejich tasku
     * @param bool $rodice Ovlivnuje jestli se nactou potomci nebo rodicovske typy
     * @return array id, zazev
     */
    public function fetchPairsMain($rodice = TRUE)
    {
        if ($rodice) {
            return $this->fetchAll()
                ->where('typ_incident IS NULL')
                ->fetchPairs();
        } else {
            return $this->fetchAll()
                ->where('typ_incident IS NOT NULL')
                ->fetchPairs();
        }
    }
}
