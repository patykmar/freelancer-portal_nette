<?php


namespace App\Model;


use dibi;

/**
 * Description of FormaUhradyModel
 *
 * @author Martin Patyk
 */
final class FormaUhradyModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'forma_uhrady';

    private static $staticName = 'forma_uhrady';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return array id, zazev
     */
    public static function fetchPairs()
    {
        return dibi::select('[id]')
            ->select('[nazev]')
            ->from('%n', FormaUhradyModel::$staticName)
            ->orderBy('nazev')
            ->fetchPairs();
    }
}