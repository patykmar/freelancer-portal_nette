<?php

namespace App\Factory\Grids;

use App\Factory\DataGridFactory;
use App\Model\SlaModel;
use Nette\Database\Context;
use Nette\Database\Table\Selection;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class SlaDataGridFactory
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
    public function create(?int $tariffId): DataGrid
    {
        $dataSource = $this->provideDataSource();
        if (!is_null($tariffId)) {
            $dataSource
                ->where('tarif', $tariffId);
        }

        $dataGrid = $this->dataGridFactory->create()->setDataSource($dataSource);
        $dataGrid->addColumnText('tarif', 'Jméno tarifu');
        $dataGrid->addColumnText('priorita', 'Priorita');
        $dataGrid->addColumnText('cenaKoe', 'Cena koeficient');
        $dataGrid->addColumnText('cenaCelkem', 'Cena za ukon celkem');

        $this->addEditButton($dataGrid);

        return $dataGrid;
    }

    private function provideDataSource(): Selection
    {
        return $this->context->table(SlaModel::TABLE_NAME)
            ->select('sla.id AS id')
            ->select('tarif.nazev AS tarif')
            ->select('priorita.nazev AS priorita')
            ->select('sla.cena_koeficient AS cenaKoe')
            ->select('sla.cena_koeficient*tarif.cena AS cenaCelkem');
    }

}