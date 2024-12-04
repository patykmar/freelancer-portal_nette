<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of FakturaPolozkaCssModel
 *
 * @author Martin Patyk
 */
final class FakturaPolozkaCssModel extends BaseModel
{
    use FetchPairsTrait;

    public const string TABLE_NAME = 'faktura_polozka_css';

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

}
