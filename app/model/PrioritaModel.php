<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of PrioritaModel
 *
 * @author Martin Patyk
 */
final class PrioritaModel extends BaseNDbModel
{
    /** @var string nazev tabulky */
    protected $tableName = 'priorita';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return array
     */
    public function fetchPairs()
    {
        return $this->fetchAll()->fetchPairs("id", "nazev");
    }
}