<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of OdCiModel
 *
 * @author Martin Patyk
 */
final class OdCiModel extends BaseNDbModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'od_ci';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

    public function fetchCiId(string $from)
    {
        return $this->explorer->table(self::TABLE_NAME)
            ->where("od", $from)
            ->limit(1)
            ->fetch();
    }

}
