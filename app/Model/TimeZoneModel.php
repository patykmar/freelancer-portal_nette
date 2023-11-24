<?php

namespace App\Model;

/**
 * Description of TimeZoneModel
 *
 * @author Martin Patyk
 */
final class TimeZoneModel extends BaseModel
{
    use FetchPairsTrait;

    /** @var string nazev tabulky */
    protected $name = 'time_zone';

}
