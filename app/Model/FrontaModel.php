<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of FrontaModel
 *
 * @author Martin Patyk
 */
final class FrontaModel extends BaseModel
{
    use FetchPairsTrait;

    public const string TABLE_NAME = 'fronta';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
