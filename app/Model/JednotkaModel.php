<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of JednotkaModel
 *
 * @author Martin Patyk
 */
final class JednotkaModel extends BaseModel
{
    public const string TABLE_NAME = 'jednotka';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }


    public function fetchPairs(): array
    {
        return $this->explorer->table(self::TABLE_NAME)
            ->order('nazev')
            ->select('id')
            ->select('CONCAT(nazev," ",zkratka) AS nazev')
            ->fetchPairs('id', 'nazev');
    }
}
