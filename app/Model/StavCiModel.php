<?php

namespace App\Model;

use dibi;
use DibiException;

/**
 * Description of StavCiModel
 *
 * @author Martin Patyk
 */
final class StavCiModel extends BaseModel
{
    use FetchPairsTrait;

    /** @var string nazev tabulky */
    protected $tableName = 'stav_ci';

}
