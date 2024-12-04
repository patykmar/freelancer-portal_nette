<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of ZpusobUzavreniModel
 *
 * @author Martin Patyk
 */
final class ZpusobUzavreniModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'zpusob_uzavreni';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
