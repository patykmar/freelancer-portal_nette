<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of StavCiModel
 *
 * @author Martin Patyk
 */
final class StavCiModel extends BaseNDbModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'stav_ci';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

}
