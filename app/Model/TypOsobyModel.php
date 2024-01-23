<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of TypOsobyModel
 *
 * @author Martin Patyk
 */
final class TypOsobyModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'typ_osoby';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }


}
