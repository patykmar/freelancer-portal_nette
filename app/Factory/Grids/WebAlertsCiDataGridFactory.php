<?php

namespace App\Factory\Grids;

use App\Factory\DataGridFactory;
use App\Model\OdCiModel;
use Nette\Database\Explorer;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class WebAlertsCiDataGridFactory
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
        $dataGrid = $this->dataGridFactory->create()->setDataSource(
            $this->explorer->table(OdCiModel::TABLE_NAME)
                ->select('od_ci.id')
                ->select('od')
                ->select('ci.nazev AS ci'));
        $dataGrid->addColumnText('od', 'Odesilatel');
        $dataGrid->addColumnText('ci', 'Nazev CI');

        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);

        return $dataGrid;
    }

}