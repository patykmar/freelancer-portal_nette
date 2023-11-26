<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of ChangeModel
 *
 * @author Martin Patyk
 */
final class ChangeModel extends BaseNDbModel
{
    public const TABLE_NAME = 'change';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

}
