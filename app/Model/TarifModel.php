<?php

namespace App\Model;

use LogicException;
use Nette\Database\Context;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

/**
 * Description of TarifModel
 *
 * @author Martin Patyk
 */
final class TarifModel extends BaseNDbModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'tarif';

    private $slaModel;

    public function __construct(Context $context, SlaModel $slaModel)
    {
        parent::__construct(self::TABLE_NAME, $context);
        $this->slaModel = $slaModel;
    }


    /**
     * Vklada data do tabulky tarif a k tomu vytvari vychozi hodnoty SLAcek
     * @param ArrayHash $values
     * @return void
     */
    public function insert(ArrayHash $values)
    {
        try {
            $this->explorer->beginTransaction();
            $this->explorer->table($this->tableName)->insert($values);

            $lastTarrif = $this->explorer->table($this->tableName)->order("id DESC")->limit(1)->fetch();
            if (is_null($lastTarrif) || !isset($lastTarrif['id'])) {
                throw new LogicException("Nove pridany tarif nebyl nalezen");
            }

            $this->slaModel->insertDefaultValue($lastTarrif['id']);
            $this->explorer->commit();
        } catch (LogicException $exc) {
            $this->explorer->rollBack();
            // zapisu chybu do logy
            Debugger::log($exc->getMessage());
            throw new InvalidArgumentException();
        }
    }
}
