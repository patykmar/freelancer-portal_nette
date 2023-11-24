<?php

namespace App\Model;

use dibi;

/**
 * Description of FakturaPolozkaModel
 *
 * @author Martin Patyk
 */
final class FakturaPolozkaModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $tableName = 'faktura_polozka';

    public function fetchAllByIdFaktura($id)
    {
        return dibi::select('[faktura_polozka].[nazev]')
            ->select('dodatek')
            ->select('cena')
            ->select('sleva')
            ->select('([faktura_polozka].[pocet_polozek] * [faktura_polozka].[koeficient_cena])')->as('pocet_polozek')
            ->select('[dph].[procent]')->as('[dph]')
            ->select('[jednotka].[zkratka]')->as('[jednotka]')
            ->select('([faktura_polozka].[cena] * [faktura_polozka].[pocet_polozek] * [faktura_polozka].[koeficient_cena] * (1-(sleva*0.01)))')->as('[cena_celkem]')
            ->select('faktura_polozka_css.nazev')->as('cssclass')
            ->from('%n', $this->tableName)
            ->leftJoin('[dph]')->on('[faktura_polozka].[dph] = [dph].[id]')
            ->leftJoin('[jednotka]')->on('[faktura_polozka].[jednotka] = [jednotka].[id]')
            ->leftJoin('[faktura_polozka_css]')->on('[faktura_polozka].[cssclass] = [faktura_polozka_css].[id]')
            ->where('faktura = %i', $id)
            ->orderBy('[faktura_polozka].[id]')
            ->fetchAll();
    }
}