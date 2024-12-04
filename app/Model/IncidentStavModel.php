<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of IncidentStavModel
 *
 * @author Martin Patyk
 */
final class IncidentStavModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'incident_stav';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

}
