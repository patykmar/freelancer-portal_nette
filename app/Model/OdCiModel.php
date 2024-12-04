<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of OdCiModel
 *
 * @author Martin Patyk
 */
final class OdCiModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'od_ci';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

    public function fetchCiId(string $from)
    {
        return $this->explorer->table(self::TABLE_NAME)
            ->where("od", $from)
            ->limit(1)
            ->fetch();
    }

}
