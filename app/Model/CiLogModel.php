<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of CiLogModel
 *
 * @author Martin Patyk
 */
final class CiLogModel extends BaseModel
{
    public const string TABLE_NAME = 'ci_log';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

    /**
     * Nacte log na zaklade CI identifikatoru
     * @param int $id identifikaotr CIcka
     * @return array
     */
    public function fetchAllByCi(int $id): array
    {
        return $this->explorer->table(self::TABLE_NAME)
            ->where('ci', $id)->fetchAll();
    }

}
