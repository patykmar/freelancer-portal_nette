<?php

namespace App\Model;

use dibi;
use DibiRow;
use Nette\Database\Context;

/**
 * Description of OsobaModel
 *
 * @author Martin Patyk
 */
final class OsobaModel extends BaseNDbModel
{
    public const TABLE_NAME = 'osoba';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }


    /**
     * Vrati v paru id a jmena pouze specialistu a systemovych uzivatelu
     * @return array
     */
    public function fetchPairsSpecialistSystem(): array
    {
        return $this->explorer->table($this->tableName)
            ->where("typ_osoby", [2, 3])
            ->fetchPairs('id', 'CONCAT([jmeno]," ",[prijmeni]) as nazev');
    }

    /**
     * Vrati v paru id a jmena pouze specialistu a systemovych uzivatelu.
     * @return DibiRow id => nazev
     * typ_osoby:
     *  1 - zakaznik
     *  2 - specialista
     *  3 - system
     */
    public static function fetchPairs()
    {
        return dibi::select('id')
            ->select('CONCAT([jmeno]," ",[prijmeni])')->as('nazev')
            ->from('%n', 'osoba')
            ->where('typ_osoby')->in('(2,3)')
            ->orderBy('prijmeni')
            ->fetchPairs();
    }

    /**
     * Metoda vraci vsechny osoby k pouziti do formulare.
     */
    public function fetchAllPairs()
    {
        $sql = "SELECT id, CONCAT(jmeno, ' ', prijmeni) AS nazev ";
        $sql .= "FROM osoba ";
        $sql .= "ORDER BY prijmeni";
        return $this->explorer->query($sql)->fetchPairs();
    }

    /**
     * Vrati Map<string, Map<int,string>>, kde klic je nazev firmy a v ni je mapa CiId => CiName
     * @return array
     */
    public function fetchAllPairsWithCompanyName()
    {
        $result = $this->explorer->table(self::TABLE_NAME)
            ->where("firma.id = osoba.firma")
            ->order("osoba.prijmeni")
            ->select("osoba.id")
            ->select('CONCAT(prijmeni," ",jmeno) AS nazev')
            ->select("firma.nazev AS nazevFirmy")
            ->fetchAssoc("nazevFirmy|id");

        foreach ($result as $k => $v) {
            foreach ($v as $key => $value) {
                $result[$k][$key] = $value['nazev'];
            }
        }

        return $result;
    }
}
