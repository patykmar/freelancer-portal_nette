<?php

namespace App\Factory\Grids;

use App\Factory\DataGridFactory;
use App\Model\IncidentModel;
use Nette\Database\Context;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class VyuctovaniDataGridFactory
{
    use DataGridFactoryTrait;

    private Context $context;
    private DataGridFactory $dataGridFactory;

    /**
     * @param Context $context
     * @param DataGridFactory $dataGridFactory
     */
    public function __construct(Context $context, DataGridFactory $dataGridFactory)
    {
        $this->context = $context;
        $this->dataGridFactory = $dataGridFactory;
    }

    /**
     * @throws DataGridException
     */
    public function create(): DataGrid
    {
        $dataGrid = $this->dataGridFactory->create()->setDataSource(
            $this->context->table(IncidentModel::TABLE_NAME)
                ->select('incident.id AS id')
                ->select('COUNT(incident.id) AS pocet_tiketu')
                ->select('ci.firma.nazev AS firma')
                ->select('ci.firma.id AS firmaid')
                ->select('ROUND(sum(ukon.cena * ovlivneni.koeficient_cena * typ_incident.koeficient_cena * priorita.koeficient_cena * zpusob_uzavreni.koeficient_cena),2) AS celkem')
                ->select('CONCAT("Generuj", " ", "fakturu") AS generujFa')
                ->group('ci.firma.id')
                ->where([
                    'incident_stav' => 5,
                    'faktura' => null,
                ])
        );

        $dataGrid->addColumnText('firma', 'Firma');
        $dataGrid->addColumnText('pocet_tiketu', 'ks');
        $dataGrid->addColumnText('celkem', 'Celkem v Kč');

        $dataGrid->addAction('generujFakturu', 'Vygeneruj fakturu', 'generujFakturu')
            ->setTitle('Vygeneruj fakturu')
            ->setClass('btn btn-info btn-sm');

        return $dataGrid;
    }
}