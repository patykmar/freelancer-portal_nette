<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of UkonModel
 *
 * @author Martin Patyk
 */
final class UkonModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'ukon';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }


}
