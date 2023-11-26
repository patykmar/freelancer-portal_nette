<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of OvlivneniModel
 *
 * @author Martin Patyk
 */
final class OvlivneniModel extends BaseNDbModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'ovlivneni';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

}
