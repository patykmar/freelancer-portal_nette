<?php

/**
 * Description of BaseNDbModel
 *
 * @author Martin Patyk
 */

namespace App\Model;

use Nette\Application\BadRequestException;
use Nette\Database\Context;
use Nette\Database\Table\IRow;
use Nette\SmartObject;
use Nette\Utils\ArrayHash;

abstract class BaseModel implements BaseModelInterface
{
    use SmartObject;

    protected string $tableName;
    protected Context $explorer;

    public function __construct(string $tableName, Context $context)
    {
        $this->tableName = $tableName;
        $this->explorer = $context;
    }

    /**
     * @param int $id
     * @return ArrayHash
     * @throws BadRequestException
     */
    public function fetchById(int $id): ArrayHash
    {
        $result = $this->explorer->table($this->tableName)->get($id);
        if ($this->checkNullOrFalse($result)) {
            throw new BadRequestException("Item with id: $id didn't found in table {$this->tableName}");
        }
        return ArrayHash::from($result);
    }

    public function fetchAll(): ArrayHash
    {
        $result = $this->explorer->table($this->tableName)->fetchAll();
        if (empty($result)) {
            throw new BadRequestException("Table {$this->tableName} is empty");
        }
        return ArrayHash::from($result);
    }

    /**
     * @param ArrayHash $newItem
     * @return bool|int|IRow
     */
    public function insertNewItem(ArrayHash $newItem): ArrayHash
    {
        return $this->explorer->table($this->tableName)->insert($newItem);
    }

    public function removeItem(int $id, string $keyName = "id"): void
    {
        $this->explorer->table($this->tableName)
            ->where($keyName, $id)
            ->delete();
    }

    /**
     * @param ArrayHash $values
     * @param int $id
     */
    public function updateItem(ArrayHash $values, int $id): void
    {
        $this->explorer->table($this->tableName)
            ->where("id", $id)
            ->update($values);
    }

    /**
     * @return bool|IRow
     * @throws BadRequestException
     */
    public function getLastId(): int
    {
        $result = $this->explorer->table($this->tableName)
            ->select('id')
            ->order('id DESC')
            ->limit(1)
            ->fetch()['id'];
        if ($this->checkNullOrFalse($result) && !is_int($result)) {
            throw new BadRequestException("No ID has been found in table: {$this->tableName}");
        }
        return $result;
    }

    /**
     * @param mixed $input
     */
    public function checkNullOrFalse($input): bool
    {
        return is_null($input) || false === $input;
    }
}
