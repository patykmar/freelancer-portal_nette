<?php

namespace App\Model;

use Nette\Application\BadRequestException;
use Nette\Utils\ArrayHash;

interface BaseModelInterface
{
    function insertNewItem(ArrayHash $newItem): ArrayHash;

    /**
     * @return ArrayHash if there is no such row
     * @throws BadRequestException
     */
    function fetchById(int $id): ArrayHash;

    /**
     * @throws BadRequestException
     */
    function fetchAll(): ArrayHash;

    function removeItem(int $id, string $keyName = 'id'): void;

    function updateItem(ArrayHash $values, int $id): void;
}
