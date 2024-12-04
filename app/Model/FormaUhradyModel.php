<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of FormaUhradyModel
 *
 * @author Martin Patyk
 */
final class FormaUhradyModel extends BaseModel
{
    use FetchPairsTrait;

    public const string TABLE_NAME = 'forma_uhrady';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
