<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of FrontaModel
 *
 * @author Martin Patyk
 */
final class FrontaModel extends BaseModel
{
    use FetchPairsTrait;

    /** @var string nazev tabulky */
    protected $tableName = 'fronta';

}
