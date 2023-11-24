<?php


namespace App\Model;


use dibi;
use DibiException;

/**
 * Description of CiLogModel
 *
 * @author Martin Patyk
 */
final class CiLogModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $tableName = 'ci_log';

    /**
     * Nacte log na zaklade CI identifikatoru
     * @param int $id identifikaotr CIcka
     * @return array of DibiRow
     */
    public function fetchAllByCi(int $id): array
    {
        return $this->explorer->table($this->tableName)->where('ci', $id)->fetchAll();
    }
}
