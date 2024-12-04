<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of ChangeModel
 *
 * @author Martin Patyk
 */
final class ChangeModel extends BaseModel
{
    public const TABLE_NAME = 'change';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
