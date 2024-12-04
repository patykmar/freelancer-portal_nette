<?php

namespace App\Factory\Grids;

use App\Factory\DataGridFactory;
use App\Model\CiModel;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class CiDataGridFactory
{
    use DataGridFactoryTrait;

    private Explorer $explorer;
    private DataGridFactory $dataGridFactory;

    /**
     * @param Explorer $explorer
     * @param DataGridFactory $dataGridFactory
     */
    public function __construct(Explorer $explorer, DataGridFactory $dataGridFactory)
    {
        $this->explorer = $explorer;
        $this->dataGridFactory = $dataGridFactory;
    }

    /**
     * @throws DataGridException
     */
    public function create(): DataGrid
    {
        $dataGrid = $this->dataGridFactory->create()
            ->setDataSource($this->getDatabaseContext()->where(['zobrazit' => true]));

        $dataGrid->addColumnText('nazev', 'Název');
        $dataGrid->addColumnText('fronta_tier_1', 'Fronta T1', 'fronta_tier_1.nazev');
        $dataGrid->addColumnText('fronta_tier_2', 'Fronta T2', 'fronta_tier_2.nazev');
        $dataGrid->addColumnText('fronta_tier_3', 'Fronta T3', 'fronta_tier_3.nazev');
        $dataGrid->addColumnText('stav_ci', 'Stav', 'stav_ci.nazev');
        $dataGrid->addColumnText('tarif', 'Tarif', 'tarif.nazev');
        $dataGrid->addColumnText('firma', 'Firma', 'firma.nazev');

        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);

        return $dataGrid;
    }

    /**
     * @throws DataGridException
     */
    public function createPotomciCi(int $id): DataGrid
    {
        $dataGrid = $this->dataGridFactory->create()
            ->setDataSource($this->getDatabaseContext()->where(['ci' => $id]));
        $dataGrid->addColumnText('nazev', 'Název');

        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);
        return $dataGrid;
    }

    private function getDatabaseContext(): Selection
    {
        return $this->explorer->table(CiModel::TABLE_NAME);
    }
}