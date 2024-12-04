<?php

namespace App\Factory\Grids;

use App\Factory\DataGridFactory;
use Nette\Database\Explorer;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class EmptyDataGridFactory
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
    public function create(string $tableName): DataGrid
    {
        $dataGrid = $this->dataGridFactory->create()
            ->setDataSource($this->explorer->table($tableName));

        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);

        return $dataGrid;
    }
}