<?php

namespace App\Model;

use dibi;
use DibiException;
use Nette\ArrayHash;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;

/**
 * Description of TarifModel
 *
 * @author Martin Patyk
 */
final class TarifModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'tarif';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return string
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [tarif] ORDER BY [nazev]');
    }

    /**
     * Vklada data do tabulky tarif a k tomu vytvari vychozi hodnoty SLAcek
     * @param ArrayHash $newItem
     * @return void
     * @throws DibiException
     */
    public function insert(ArrayHash $newItem)
    {
        try {
            dibi::begin();
            dibi::query('INSERT INTO %n', $this->name, '%v', $newItem);
            $slaModel = new SlaModel();
            $slaModel->insertDefaultValue($this->getLastId());
            dibi::commit();
        } catch (DibiException $exc) {
            dibi::rollback();
            // zapisu chybu do logy
            Debugger::log($exc->getMessage());
            throw new InvalidArgumentException();
        }
    }
}