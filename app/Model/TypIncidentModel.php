<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of TypIncidentModel
 *
 * @author Martin Patyk
 */
final class TypIncidentModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'typ_incident';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

    /**
     * Vrati vsechny hlavni typy tyketu bez jejich tasku
     * @param bool $rodice Ovlivnuje jestli se nactou potomci nebo rodicovske typy
     * @return array id, zazev
     */
    public function fetchPairsMain($rodice = true): array
    {
        if ($rodice) {
            return $this->explorer->table($this->tableName)
                ->where('typ_incident IS null')
                ->fetchPairs('id', 'nazev');
        } else {
            return $this->explorer->table($this->tableName)
                ->where('typ_incident IS NOT null')
                ->fetchPairs('id', 'nazev');
        }
    }
}
