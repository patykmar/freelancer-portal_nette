<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of ZemeModel
 *
 * @author Martin Patyk
 */
final class ZemeModel extends BaseModel
{
    use FetchPairsTrait;

    public const string TABLE_NAME = 'zeme';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
