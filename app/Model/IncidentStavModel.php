<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of IncidentStavModel
 *
 * @author Martin Patyk
 */
final class IncidentStavModel extends BaseModel
{
    use FetchPairsTrait;

    public const string TABLE_NAME = 'incident_stav';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
