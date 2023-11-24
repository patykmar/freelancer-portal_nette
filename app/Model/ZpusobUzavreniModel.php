<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of ZpusobUzavreniModel
 *
 * @author Martin Patyk
 */
final class ZpusobUzavreniModel extends BaseNDbModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'zpusob_uzavreni';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

}
