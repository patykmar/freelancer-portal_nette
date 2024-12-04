<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of FakturaPolozkaCssModel
 *
 * @author Martin Patyk
 */
final class FakturaPolozkaCssModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'faktura_polozka_css';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

}
