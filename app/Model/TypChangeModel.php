<?php

namespace App\Model;


use Nette\Database\Context;

/**
 * Description of TypChangeModel
 *
 * @author Martin Patyk
 */
final class TypChangeModel extends BaseNDbModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'typ_change';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

}
