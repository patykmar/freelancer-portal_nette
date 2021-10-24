<?php

/**
 * Description of BaseNDbModel
 *
 * @author Martin Patyk
 */

namespace App\Model;

use Nette;
use Nette\Database\Context;
use Nette\Database\SqlLiteral;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\IRow;
use Nette\Database\Table\Selection;

abstract class BaseNDbModel extends Nette\Object
{
    /** @var string */
    protected $tableName;

    /** @var Context */
    protected $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
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
        return $this->database->table($this->tableName);
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