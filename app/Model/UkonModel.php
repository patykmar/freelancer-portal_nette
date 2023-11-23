<?php

namespace App\Model;

use dibi;

/**
 * Description of UkonModel
 *
 * @author Martin Patyk
 */
final class UkonModel extends BaseNDbModel
{
    /** @var string nazev tabulky */
    protected $tableName = 'ukon';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return array id, zazev
     */
    public function fetchPairs()
    {
        return $this->fetchAll()
            ->order('nazev')
            ->fetchPairs('id', 'nazev');
    }
}