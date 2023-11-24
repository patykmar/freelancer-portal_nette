<?php

/**
 * Description of BaseNDbModel
 *
 * @author Martin Patyk
 */

namespace App\Model;

use Nette\Object;
use Nette\Database\Context;
use Nette\Database\SqlLiteral;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\IRow;
use Nette\Database\Table\Selection;

abstract class BaseNDbModel extends Object
{
    /** @var string $tableName */
    protected $tableName;

    /** @var Context */
    protected $explorer;

    public function __construct(Context $database)
    {
        $this->explorer = $database;
    }

    /**
     * @param string $name
     */
    protected function setTableName($name)
    {
        $this->tableName = $name;
    }

    /**
     * @param int $id
     * @return ActiveRow
     */
    public function fetch($id)
    {
        return $this->fetchAll()->get($id);
    }

    /**
     * @return Selection
     */
    public function fetchAll()
    {
        return $this->explorer->table($this->tableName);
    }

    /**
     * @param $values
     * @return bool|int|SqlLiteral|IRow
     */
    public function insert($values)
    {
        return $this->fetchAll()->insert($values);
    }
}
