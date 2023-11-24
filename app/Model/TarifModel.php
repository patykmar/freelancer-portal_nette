<?php

namespace App\Model;

use dibi;
use DibiException;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

/**
 * Description of TarifModel
 *
 * @author Martin Patyk
 */
final class TarifModel extends BaseModel
{
    use FetchPairsTrait;

    /** @var string nazev tabulky */
    protected $tableName = 'tarif';

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
            dibi::query('INSERT INTO %n', $this->tableName, '%v', $newItem);
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
