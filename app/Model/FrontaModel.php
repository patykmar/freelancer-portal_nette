<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of FrontaModel
 *
 * @author Martin Patyk
 */
final class FrontaModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'fronta';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

}
