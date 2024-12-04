<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of PrioritaModel
 *
 * @author Martin Patyk
 */
final class PrioritaModel extends BaseModel
{
    use FetchPairsTrait;

    public const string TABLE_NAME = 'priorita';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
