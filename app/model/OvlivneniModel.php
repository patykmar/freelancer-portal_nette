<?php

namespace App\Model;

use DibiException;

/**
 * Description of OvlivneniModel
 *
 * @author Martin Patyk
 */
final class OvlivneniModel extends BaseNDbModel
{
    /** @var string nazev tabulky */
    protected $name = 'ovlivneni';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return array
     */
    public function fetchPairs()
    {
        return $this->fetchAll()->fetchPairs('id', 'nazev');
    }
}