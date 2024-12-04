<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of TimeZoneModel
 *
 * @author Martin Patyk
 */
final class TimeZoneModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'time_zone';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
