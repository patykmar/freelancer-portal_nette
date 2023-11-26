<?php

namespace App\Model;

use Nette\Database\Context;
use stdClass;

/**
 * Description of FrontaOsobaModel
 *
 * @author Martin Patyk
 */
final class FrontaOsobaModel extends BaseNDbModel
{
    public const TABLE_NAME = 'fronta_osoba';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }

    /**
     * Vrati Map<string, Map<int,string>>, kde klic je nazev fronty a v ni je mapa OsobaId => OsobaName
     * @return array|object|stdClass
     * typ_osoby:
     *  1 - zakaznik
     *  2 - specialista
     *  3 - system
     */
    public function fetchSpecialistPairsWithQueueName()
    {
        $result = $this->explorer->table(self::TABLE_NAME)
            ->where("fronta.id = fronta_osoba.fronta")
            ->where("osoba.id = fronta_osoba.osoba")
            ->select("fronta_osoba.id")
            ->select('CONCAT(osoba.prijmeni," ",osoba.jmeno) AS osoba')
            ->select("fronta.nazev AS fronta")
            ->fetchAssoc("fronta|id");

        foreach ($result as $k => $v):
            foreach ($v as $key => $value):
                $result[$k][$key] = $value['osoba'];
            endforeach;
        endforeach;

        return $result;
    }

    public function fetchAllWithOsobaAndFrontaName()
    {
        return $this->explorer->table(self::TABLE_NAME)
            ->where("fronta.id = fronta_osoba.fronta")
            ->where("osoba.id = fronta_osoba.osoba")
            ->select("fronta_osoba.id")
            ->select('CONCAT(osoba.jmeno," ",osoba.prijmeni) AS jmeno')
            ->select('fronta.nazev AS fronta_nazev')
            ->fetchAssoc('id');
    }

}
