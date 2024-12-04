<?php

namespace App\Model;

use Nette\Database\Explorer;

/**
 * Description of CiModel
 *
 * @author Martin Patyk
 */
final class CiModel extends BaseModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = "ci";

    public function __construct(Explorer $explorer)
    {
        parent::__construct(self::TABLE_NAME, $explorer);
    }

    /**
     * Vrati Map<string, Map<int,string>>, kde klic je nazev firmy a v ni je mapa CiId => CiName
     * @return array
     */
    public function fetchAllPairsWithCompanyName(): array
    {
        $result = $this->explorer->table(self::TABLE_NAME)
            ->where("firma.id = ci.firma")
            ->order("firma.nazev")
            ->select("ci.id")
            ->select("ci.nazev")
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
