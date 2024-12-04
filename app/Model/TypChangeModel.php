<?php

namespace App\Model;


use Nette\Database\Explorer;

/**
 * Description of TypChangeModel
 *
 * @author Martin Patyk
 */
final class TypChangeModel extends BaseModel
{
    use FetchPairsTrait;

    public const string TABLE_NAME = 'typ_change';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
