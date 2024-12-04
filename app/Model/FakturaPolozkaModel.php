<?php

namespace App\Model;

use Nette\Database\Connection;
use Nette\Database\Context;

/**
 * Description of FakturaPolozkaModel
 *
 * @author Martin Patyk
 */
final class FakturaPolozkaModel extends BaseModel
{
    public const TABLE_NAME = 'faktura_polozka';
    private $connection;

    public function __construct(Context $context, Connection $connection)
    {
        parent::__construct(self::TABLE_NAME, $context);
        $this->connection = $connection;
    }

    public function fetchAllByIdFaktura($id): array
    {
        $query = "SELECT " .
            "faktura_polozka.nazev, dodatek, cena, sleva, dph.procent AS dph, jednotka.zkratka AS jednotka, " .
            "(faktura_polozka.pocet_polozek * faktura_polozka.koeficient_cena) AS pocet_polozek, " .
            "(faktura_polozka.cena * faktura_polozka.pocet_polozek * faktura_polozka.koeficient_cena * (1-(sleva*0.01))) AS cena_celkem, " .
            "faktura_polozka_css.nazev AS cssclass " .
            "FROM " . self::TABLE_NAME . " " .
            "LEFT JOIN dph ON faktura_polozka.dph = dph.id " .
            "LEFT JOIN jednotka ON faktura_polozka.jednotka = jednotka.id " .
            "LEFT JOIN faktura_polozka_css ON faktura_polozka.cssclass = faktura_polozka_css.id " .
            "WHERE faktura = ? " .
            "ORDER BY faktura = faktura_polozka.id ";

        return $this->connection->query($query, $id)->fetchAll();
    }

}
