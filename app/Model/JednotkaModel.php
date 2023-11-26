<?php

namespace App\Model;

/**
 * Description of JednotkaModel
 *
 * @author Martin Patyk
 */
final class JednotkaModel extends BaseNDbModel
{
    use FetchPairsTrait {
        fetchPairs as protected traitFetchPairs;
    }

    public const TABLE_NAME = 'jednotka';

    public function fetchPairs(): array
    {
        return $this->traitFetchPairs('CONCAT(nazev," ",zkratka) AS nazev');
    }
}
