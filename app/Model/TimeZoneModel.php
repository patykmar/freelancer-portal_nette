<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of TimeZoneModel
 *
 * @author Martin Patyk
 */
final class TimeZoneModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'time_zone';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

}
