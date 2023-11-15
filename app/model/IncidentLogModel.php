<?php

namespace App\Model;

use dibi;
use DibiException;
use Nette\Utils\ArrayHash;

/**
 * Description of IncidentLogModel
 *
 * @author Martin Patyk
 */
final class IncidentLogModel extends BaseModel
{
    /** @var string nazev tabulky */
    protected $name = 'incident_log';

    public function fetchAllByIncidentId($id)
    {
        return dibi::select('incident_log.*, osoba.jmeno, osoba.prijmeni')
            ->from('%n', $this->name)
            ->leftJoin('osoba')->on('([incident_log].[osoba] = [osoba].[id])')
            ->where('incident = %i', $id)
            ->orderBy('datum_vytvoreni')->desc()
            ->fetchAll();
    }

    /**
     * @param ArrayHash $newItem
     * @throws DibiException
     */
    public function insert(ArrayHash $newItem)
    {
        parent::insert($newItem);
    }
}
