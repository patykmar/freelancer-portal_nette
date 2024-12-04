<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of StavCiModel
 *
 * @author Martin Patyk
 */
final class StavCiModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'stav_ci';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
