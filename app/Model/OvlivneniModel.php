<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of OvlivneniModel
 *
 * @author Martin Patyk
 */
final class OvlivneniModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'ovlivneni';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
