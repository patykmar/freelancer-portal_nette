<?php

/**
 * Description of BaseNDbModel
 *
 * @author Martin Patyk
 */

namespace App\Model;

use Nette\Database\Context;
use Nette\Database\Table\IRow;
use Nette\SmartObject;
use Nette\Utils\ArrayHash;

abstract class BaseNDbModel
{
    use SmartObject;

    protected $tableName;
    protected $explorer;

    public function __construct(string $tableName, Context $context)
    {
        $this->tableName = $tableName;
        $this->explorer = $context;
    }

    /**
     * @param int $id
     * @return IRow
     */
    public function fetch(int $id): IRow
    {
        return $this->explorer->table($this->tableName)->get($id);
    }

    public function fetchAll(): array
    {
        return $this->explorer->table($this->tableName)->fetchAll();
    }

    /**
     * @param ArrayHash $values
     * @return bool|int|IRow
     */
    public function insert(ArrayHash $values)
    {
        return $this->explorer->table($this->tableName)->insert($values);
    }

    public function removeItem(int $id)
    {
        $this->explorer->table($this->tableName)
            ->where("id", $id)
            ->delete();
    }

    /**
     * @param ArrayHash $arr
     * @param int $id
     */
    public function update(ArrayHash $arr, int $id)
    {
        $this->explorer->table($this->tableName)
            ->where("id", $id)
            ->update($arr);
    }

    /**
     * @return bool|IRow
     */
    public function getLastId()
    {
        return $this->explorer->table($this->tableName)
            ->select('id')
            ->order('id DESC')
            ->limit(1)
            ->fetch()['id'];
    }
}
