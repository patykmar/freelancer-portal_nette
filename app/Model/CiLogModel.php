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
    protected $name = 'ci_log';


    /**
     * Nacte log na zaklade CI identifikatoru
     * @param int $id identifikaotr CIcka
     * @return array of DibiRow
     * @throws DibiException
     */
    public function fetchAllByCi($id)
    {
        return dibi::fetchAll('SELECT * FROM %n', $this->name, ' WHERE [ci]=%i', $id);
    }
}