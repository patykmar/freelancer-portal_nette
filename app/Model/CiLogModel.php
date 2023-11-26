<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of CiLogModel
 *
 * @author Martin Patyk
 */
final class CiLogModel extends BaseNDbModel
{
    public const TABLE_NAME = 'ci_log';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

    /**
     * Nacte log na zaklade CI identifikatoru
     * @param int $id identifikaotr CIcka
     * @return array
     */
    public function fetchAllByCi(int $id): array
    {
        return $this->explorer->table($this->tableName)
            ->where('ci', $id)->fetchAll();
    }

}
