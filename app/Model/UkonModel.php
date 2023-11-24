<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of UkonModel
 *
 * @author Martin Patyk
 */
final class UkonModel extends BaseNDbModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'ukon';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }


}
