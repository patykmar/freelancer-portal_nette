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
    public const string TABLE_NAME = 'change';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
