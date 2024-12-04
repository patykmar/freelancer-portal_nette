<?php

namespace App\Factory\Grids;

use App\Factory\DataGridFactory;
use App\Model\UkonModel;
use Nette\Database\Explorer;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class UkonDataGridFactory
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
            ->setDataSource($this->explorer->table(UkonModel::TABLE_NAME));

        $dataGrid->addColumnText('nazev', 'Název');
        $dataGrid->addColumnText('cena', 'Cena');

        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);

        return $dataGrid;
    }
}