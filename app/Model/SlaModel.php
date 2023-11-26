<?php

namespace App\Model;

use Nette\Database\Context;
use Nette\Database\Table\IRow;

/**
 * Description of SlaModel
 *
 * @author Martin Patyk
 */
final class SlaModel extends BaseNDbModel
{
    use FetchPairsTrait;

    public const TABLE_NAME = 'sla';
    private $prioritaModel;
    private $typIncidentModel;

    public function __construct(Context $context, PrioritaModel $prioritaModel, TypIncidentModel $typIncidentModel)
    {
        parent::__construct(self::TABLE_NAME, $context);
        $this->prioritaModel = $prioritaModel;
        $this->typIncidentModel = $typIncidentModel;
    }


    /**
     * Funkce slouzi k zjisteni pritomnosti SLAcek v tabulce k danemu tarifu
     * @param int $id identifikator tarifu
     */
    public function fetchSlaByTarif(int $id)
    {
        return $this->explorer->table($this->tableName)
            ->where("tarif", $id)
            ->limit(1)->fetch();
    }

    /**
     * Pretizena funkce ktera krome vsech hodnot v tabulce SLA vraci i nazvy
     * tarifu a priority
     * @param int $id Identifikator SLAcka
     * @return IRow
     */
    public function fetch(int $id): IRow
    {
        $sla = $this->explorer->table(self::TABLE_NAME);
        $sla->where("tarif.id = sla.tarif");
        $sla->where("priorita.id = sla.priorita");
        return $sla->get($id);
    }

    /**
     * Vstupem je identifikator prave pridaneho tarifu. K tomuto tarifu se
     * vygeneruji slacka pro kazdou prioritu.
     * @param int $idTarif
     */
    public function insertDefaultValue(int $idTarif)
    {
        $cenaKoeficient = 0.5;
        $priority = $this->prioritaModel->fetchPairs();
        $typIncidentu = $this->typIncidentModel->fetchPairs();

        $priorityAndTypIncidentu = array();

        foreach ($priority as $key1 => $item1) {
            foreach ($typIncidentu as $key2 => $item2) {
                $priorityAndTypIncidentu[] = [
                    'priorita' => $key1,
                    'typ_incident' => $key2
                ];
            }
        }

        foreach ($priorityAndTypIncidentu as $value) {
            $this->explorer->table(self::TABLE_NAME)
                ->insert([
                    'reakce_mesic' => 3,
                    'reakce_den' => 0,
                    'reakce_hod' => 0,
                    'reakce_min' => 0,
                    'hotovo_mesic' => 6,
                    'hotovo_den' => 0,
                    'hotovo_hod' => 0,
                    'hotovo_min' => 0,
                    'tarif' => $idTarif,
                    'priorita' => $value['priorita'],
                    'typ_incident' => $value['typ_incident'],
                    'cena_koeficient' => $cenaKoeficient,
                ]);
            $cenaKoeficient += 0.25;
        }
    }
}
