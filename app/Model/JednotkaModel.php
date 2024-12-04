<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of JednotkaModel
 *
 * @author Martin Patyk
 */
final class JednotkaModel extends BaseModel
{
    public const TABLE_NAME = 'jednotka';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
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
