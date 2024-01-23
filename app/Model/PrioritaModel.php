<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of PrioritaModel
 *
 * @author Martin Patyk
 */
final class PrioritaModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'priorita';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

}
