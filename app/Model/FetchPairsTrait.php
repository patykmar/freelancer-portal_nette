<?php

namespace App\Model;

trait FetchPairsTrait
{
    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @param string $value
     * @return array
     */
    public function fetchPairs(string $value = "nazev"): array
    {
        return $this->explorer->table(self::TABLE_NAME)->order('nazev')->fetchPairs('id', $value);
    }
}
