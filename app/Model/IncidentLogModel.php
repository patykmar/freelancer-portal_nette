<?php

namespace App\Model;

use Nette\Database\Context;

/**
 * Description of IncidentLogModel
 *
 * @author Martin Patyk
 */
final class IncidentLogModel extends BaseNDbModel
{
    public const TABLE_NAME = 'incident_log';

    public function __construct(Context $context)
    {
        parent::__construct(self::TABLE_NAME, $context);
    }


    /**
     * @param int $id
     * @return array
     */
    public function fetchAllByIncidentId(int $id): array
    {
        return $this->explorer->table(self::TABLE_NAME)
            ->where("incident_log.osoba = osoba.id")
            ->where("incident", $id)
            ->order("datum_vytvoreni DESC")
            ->select("incident_log.*")
            ->select("osoba.jmeno")
            ->select("osoba.prijmeni")
            ->fetchAll();
    }

}
