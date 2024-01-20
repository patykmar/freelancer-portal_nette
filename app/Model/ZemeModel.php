<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of ZemeModel
 *
 * @author Martin Patyk
 */
final class ZemeModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'zeme';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

}
