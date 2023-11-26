<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of FormaUhradyModel
 *
 * @author Martin Patyk
 */
final class FormaUhradyModel extends BaseNDbModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'forma_uhrady';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

}
