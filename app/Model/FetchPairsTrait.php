<?php

namespace App\Model;

trait FetchPairsTrait
{
    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @param string $value
     * @return array
     */
    public function fetchPairs($value = "nazev"): array
    {
        return $this->explorer->table($this->tableName)->order('nazev')->fetchPairs('id', $value);
    }
}
