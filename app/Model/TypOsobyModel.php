<?php

namespace App\Model;

/**
 * Description of TypOsobyModel
 *
 * @author Martin Patyk
 */
final class TypOsobyModel extends BaseModel
{
    use FetchPairsTrait;

    /** @var string nazev tabulky */
    protected $tableName = 'typ_osoby';

}
