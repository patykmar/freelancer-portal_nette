<?php

namespace App\Model;

use dibi;
use DibiException;
use Nette\ArrayHash;

/**
 * Description of SlaModel
 *
 * @author Martin Patyk
 */
final class SlaModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'sla';

    /**
     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari
     * @return string
     * @throws DibiException
     */
    public static function fetchPairs()
    {
        return dibi::fetchPairs('SELECT [id], [nazev] FROM [sla] ORDER BY [nazev]');
    }

    /**
     * Funkce slouzi k zjisteni pritomnosti SLAcek v tabulce k danemu tarifu
     * @param int $id identifikator tarifu
     */
    public function fetchSlaByTarif($id)
    {
        return dibi::fetch('SELECT [id] FROM %n', $this->name, 'WHERE [tarif] = %i', 'LIMIT 1');
    }

    /**
     * Pretizena funkce ktera krome vsech hodnot v tabulce SLA vraci i nazvy
     * tarifu a priority
     * @param int $id Identifikator SLAcka
     * @throws DibiException
     */
    public function fetch($id)
    {
        return dibi::fetch('SELECT [sla].*, [tarif].[nazev] AS [tarif], [priorita].[nazev] AS [priorita]',
            'FROM %n', $this->name,
            'LEFT JOIN ([tarif], [priorita]) ON ([tarif].[id]=[sla].[tarif] AND [priorita].[id]=[sla].[priorita])',
            'WHERE %n=%i LIMIT 1', $this->name . '.' . $this->primary, $id);
    }


    /**
     * Funkce vklada vice slacek k tarifu najednou
     * @throws DibiException
     */
    public function insert(ArrayHash $newItem)
    {
        foreach ($newItem as $item) {
            dump(dibi::query('INSERT INTO %n', $this->name, '%v', $item));
        }
    }

    /**
     * Vstupem je identifikator prave pridaneho tarifu. K tomuto tarifu se
     * vygeneruji slacka pro kazdou prioritu.
     * @throws DibiException
     */
    public function insertDefaultValue($id)
    {
        $cenaKoeficient = 0.5;
        dump(PrioritaModel::fetchPairs());

        foreach (PrioritaModel::fetchPairs() as $key => $value) {
            dibi::test('INSERT INTO %n', $this->name, '%v',
                array(
                    'reakce_mesic' => 3,
                    'reakce_den' => 0,
                    'reakce_hod' => 0,
                    'reakce_min' => 0,
                    'hotovo_mesic' => 6,
                    'hotovo_den' => 0,
                    'hotovo_hod' => 0,
                    'hotovo_min' => 0,
                    'tarif' => $id,
                    'priorita' => $key,
                    'cena_koeficient' => $cenaKoeficient,
                ));
            $cenaKoeficient += 0.25;
        }
        exit;
    }
}